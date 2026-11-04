<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Request;
use Tavaan\Engine\Http\Response;
use Tavaan\Engine\Http\HttpKernel;
use Tavaan\Engine\Http\HttpServiceProvider;

final class HttpFeatureTest extends TestCase
{
    public function test_end_to_end_request_response_cycle(): void
    {
        $provider = new HttpServiceProvider([]);
        $provider->register();
        $kernel = $provider->getKernel();
        $kernel->setDispatcher(function (Request $req): Response {
            if ($req->getPath() === '/api/users') {
                return Response::json(['users' => ['Ali', 'Sara']]);
            }
            return Response::notFound('Not found');
        });

        // API request
        $apiReq = Request::create('/api/users', 'GET', [], ['Accept' => 'application/json']);
        $res = $kernel->handle($apiReq);
        $this->assertSame(200, $res->getStatus());
        $this->assertSame('application/json', $res->getHeader('Content-Type'));
        $data = \json_decode($res->getContent(), true);
        $this->assertSame(['Ali', 'Sara'], $data['users']);

        // 404
        $notFoundReq = Request::create('/unknown');
        $res2 = $kernel->handle($notFoundReq);
        $this->assertSame(404, $res2->getStatus());
    }

    public function test_middleware_chain(): void
    {
        $kernel = new HttpKernel(fn() => Response::text('ok'));
        $kernel->addMiddleware(fn(Request $r) => null);
        $kernel->addMiddleware(function (Request $r) {
            $r->attributes->set('authenticated', true);
            return null;
        });
        $req = Request::create('/');
        $res = $kernel->handle($req);
        $this->assertTrue($req->attributes->get('authenticated'));
        $this->assertSame('ok', $res->getContent());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/http.php';
        $this->assertArrayHasKey('middleware', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/http.php';
        $this->assertArrayHasKey('error_request', $m);
    }
}
