<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Request;
use Tavaan\Engine\Http\Response;
use Tavaan\Engine\Http\Uri;
use Tavaan\Engine\Http\ParameterBag;
use Tavaan\Engine\Http\HeaderBag;
use Tavaan\Engine\Http\HttpKernel;
use Tavaan\Engine\Http\HttpServiceProvider;

/**
 * تست‌های Unit برای موتور Http.
 *
 * @see docs/03-engines/http.md (DOC-039)
 */
final class HttpTest extends TestCase
{
    public function test_parameter_bag_get_set(): void
    {
        $bag = new ParameterBag(['name' => 'Ali']);
        $this->assertSame('Ali', $bag->get('name'));
        $this->assertNull($bag->get('missing'));
        $this->assertSame('def', $bag->get('missing', 'def'));
        $bag->set('age', 25);
        $this->assertSame(25, $bag->get('age'));
    }

    public function test_parameter_bag_has_remove(): void
    {
        $bag = new ParameterBag(['x' => 1]);
        $this->assertTrue($bag->has('x'));
        $bag->remove('x');
        $this->assertFalse($bag->has('x'));
    }

    public function test_parameter_bag_all(): void
    {
        $bag = new ParameterBag(['a' => 1, 'b' => 2]);
        $this->assertSame(['a' => 1, 'b' => 2], $bag->all());
        $this->assertSame(2, $bag->count());
    }

    public function test_parameter_bag_string(): void
    {
        $bag = new ParameterBag(['name' => 'Ali', 'n' => null]);
        $this->assertSame('Ali', $bag->string('name'));
        $this->assertSame('def', $bag->string('n', 'def'));
        $this->assertSame('42', $bag->string('x', '42'));
    }

    public function test_parameter_bag_int(): void
    {
        $bag = new ParameterBag(['age' => '25', 'invalid' => 'abc']);
        $this->assertSame(25, $bag->int('age'));
        $this->assertSame(0, $bag->int('invalid'));
        $this->assertSame(42, $bag->int('missing', 42));
    }

    public function test_parameter_bag_bool(): void
    {
        $bag = new ParameterBag(['a' => true, 'b' => 'true', 'c' => 1, 'd' => 'false']);
        $this->assertTrue($bag->bool('a'));
        $this->assertTrue($bag->bool('b'));
        $this->assertTrue($bag->bool('c'));
        $this->assertFalse($bag->bool('d'));
        $this->assertTrue($bag->bool('missing', true));
    }

    public function test_header_bag_case_insensitive(): void
    {
        $bag = new HeaderBag(['Content-Type' => 'application/json']);
        $this->assertSame('application/json', $bag->get('Content-Type'));
        $this->assertSame('application/json', $bag->get('content-type'));
        $this->assertSame('application/json', $bag->get('CONTENT-TYPE'));
    }

    public function test_header_bag_set_has_remove(): void
    {
        $bag = new HeaderBag([]);
        $bag->set('X-Custom', 'value');
        $this->assertTrue($bag->has('X-Custom'));
        $bag->remove('X-Custom');
        $this->assertFalse($bag->has('X-Custom'));
    }

    public function test_header_bag_all(): void
    {
        $bag = new HeaderBag(['X-One' => '1', 'X-Two' => '2']);
        $all = $bag->all();
        $this->assertSame('1', $all['x-one']);
        $this->assertSame('2', $all['x-two']);
    }

    public function test_uri_parse(): void
    {
        $uri = new Uri('https://example.com:8080/path/to/page?query=1#fragment');
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/to/page', $uri->getPath());
        $this->assertSame('query=1', $uri->getQuery());
        $this->assertSame('fragment', $uri->getFragment());
    }

    public function test_uri_to_string(): void
    {
        $uri = new Uri('http://example.com/path?q=1');
        $this->assertSame('http://example.com/path?q=1', (string) $uri);
    }

    public function test_uri_empty(): void
    {
        $uri = new Uri();
        $this->assertSame('', $uri->getScheme());
        $this->assertSame('', $uri->getHost());
    }

    public function test_request_create(): void
    {
        $req = Request::create('https://example.com/users?name=Ali', 'GET');
        $this->assertSame('GET', $req->getMethod());
        $this->assertSame('/users', $req->getPath());
        $this->assertSame('Ali', $req->query->get('name'));
    }

    public function test_request_method_helpers(): void
    {
        $get = Request::create('/', 'GET');
        $post = Request::create('/', 'POST');
        $put = Request::create('/', 'PUT');
        $delete = Request::create('/', 'DELETE');
        $this->assertTrue($get->isGet());
        $this->assertTrue($post->isPost());
        $this->assertTrue($put->isPut());
        $this->assertTrue($delete->isDelete());
        $this->assertFalse($get->isPost());
    }

    public function test_request_input(): void
    {
        $req = Request::create('/', 'POST', ['name' => 'Ali']);
        $this->assertSame('Ali', $req->input('name'));
        $this->assertSame('Ali', $req->input('name', 'default'));
        $this->assertSame('def', $req->input('missing', 'def'));
    }

    public function test_request_all(): void
    {
        $req = Request::create('/?a=1', 'POST', ['b' => 2]);
        $this->assertSame(['a' => '1', 'b' => 2], $req->all());
    }

    public function test_request_header(): void
    {
        $req = Request::create('/', 'GET', [], ['Accept' => 'application/json']);
        $this->assertSame('application/json', $req->header('Accept'));
        $this->assertSame('application/json', $req->header('accept'));
    }

    public function test_request_json(): void
    {
        $req = Request::create('/', 'POST', [], [], '{"name":"Ali","age":25}');
        $this->assertSame('Ali', $req->json('name'));
        $this->assertSame(25, $req->json('age'));
    }

    public function test_request_json_nested(): void
    {
        $req = Request::create('/', 'POST', [], [], '{"user":{"id":42}}');
        $this->assertSame(42, $req->json('user.id'));
    }

    public function test_request_json_invalid(): void
    {
        $req = Request::create('/', 'POST', [], [], 'not json');
        $this->assertNull($req->json());
    }

    public function test_request_cookie(): void
    {
        $req = Request::create('/', 'GET', [], ['session' => 'abc123']);
        $this->assertSame('abc123', $req->cookie('session'));
    }

    public function test_request_expects_json(): void
    {
        $req = Request::create('/', 'GET', [], ['Accept' => 'application/json']);
        $this->assertTrue($req->expectsJson());
        $req2 = Request::create('/', 'GET');
        $this->assertFalse($req2->expectsJson());
    }

    public function test_request_get_acceptable_content_types(): void
    {
        $req = Request::create('/', 'GET', [], ['Accept' => 'text/html, application/json']);
        $types = $req->getAcceptableContentTypes();
        $this->assertContains('text/html', $types);
        $this->assertContains('application/json', $types);
    }

    public function test_request_attributes(): void
    {
        $req = Request::create('/');
        $req->attributes->set('user_id', 42);
        $this->assertSame(42, $req->attributes->get('user_id'));
    }

    // === Response ===

    public function test_response_basic(): void
    {
        $res = new Response(200, 'Hello', ['Content-Type' => 'text/plain']);
        $this->assertSame(200, $res->getStatus());
        $this->assertSame('Hello', $res->getContent());
        $this->assertSame('text/plain', $res->header('Content-Type'));
    }

    public function test_response_is_successful(): void
    {
        $this->assertTrue((new Response(200))->isSuccessful());
        $this->assertTrue((new Response(201))->isSuccessful());
        $this->assertFalse((new Response(400))->isSuccessful());
        $this->assertFalse((new Response(500))->isSuccessful());
    }

    public function test_response_is_client_error(): void
    {
        $this->assertTrue((new Response(400))->isClientError());
        $this->assertTrue((new Response(404))->isClientError());
        $this->assertFalse((new Response(500))->isClientError());
    }

    public function test_response_is_server_error(): void
    {
        $this->assertTrue((new Response(500))->isServerError());
        $this->assertFalse((new Response(400))->isServerError());
    }

    public function test_response_status_helpers(): void
    {
        $this->assertTrue((new Response(200))->isOk());
        $this->assertTrue(Response::notFound()->isNotFound());
        $this->assertTrue(Response::forbidden()->isForbidden());
        $this->assertTrue(Response::unauthorized()->isUnauthorized());
    }

    public function test_response_set_status(): void
    {
        $res = new Response(200);
        $res->setStatus(404);
        $this->assertSame(404, $res->getStatus());
    }

    public function test_response_set_content(): void
    {
        $res = new Response(200);
        $res->setContent('new content');
        $this->assertSame('new content', $res->getContent());
    }

    public function test_response_set_header(): void
    {
        $res = new Response(200);
        $res->setHeader('X-Custom', 'value');
        $this->assertSame('value', $res->getHeader('X-Custom'));
    }

    public function test_response_json_factory(): void
    {
        $res = Response::json(['name' => 'Ali']);
        $this->assertSame(200, $res->getStatus());
        $this->assertSame('application/json', $res->getHeader('Content-Type'));
        $data = \json_decode($res->getContent(), true);
        $this->assertSame('Ali', $data['name']);
    }

    public function test_response_text_factory(): void
    {
        $res = Response::text('Hello');
        $this->assertSame('Hello', $res->getContent());
        $this->assertSame('text/plain', $res->getHeader('Content-Type'));
    }

    public function test_response_html_factory(): void
    {
        $res = Response::html('<h1>Hi</h1>');
        $this->assertSame('<h1>Hi</h1>', $res->getContent());
        $this->assertSame('text/html', $res->getHeader('Content-Type'));
    }

    public function test_response_redirect_factory(): void
    {
        $res = Response::redirect('https://example.com');
        $this->assertSame(302, $res->getStatus());
        $this->assertSame('https://example.com', $res->getHeader('Location'));
    }

    public function test_response_not_found_factory(): void
    {
        $res = Response::notFound('Page not found');
        $this->assertSame(404, $res->getStatus());
        $this->assertSame('Page not found', $res->getContent());
    }

    public function test_response_no_content_factory(): void
    {
        $res = Response::noContent();
        $this->assertSame(204, $res->getStatus());
        $this->assertSame('', $res->getContent());
    }

    public function test_response_get_status_text(): void
    {
        $this->assertSame('OK', (new Response(200))->getStatusText());
        $this->assertSame('Not Found', (new Response(404))->getStatusText());
        $this->assertSame('Internal Server Error', (new Response(500))->getStatusText());
    }

    // === HttpKernel ===

    public function test_kernel_default_dispatcher(): void
    {
        $kernel = new HttpKernel();
        $req = Request::create('/');
        $res = $kernel->handle($req);
        $this->assertInstanceOf(Response::class, $res);
        $this->assertSame('Hello from Tavaan', $res->getContent());
    }

    public function test_kernel_custom_dispatcher(): void
    {
        $kernel = new HttpKernel(fn(Request $r) => Response::text('Custom response'));
        $res = $kernel->handle(Request::create('/'));
        $this->assertSame('Custom response', $res->getContent());
    }

    public function test_kernel_dispatcher_returns_string(): void
    {
        $kernel = new HttpKernel(fn(Request $r) => 'string response');
        $res = $kernel->handle(Request::create('/'));
        $this->assertSame('string response', $res->getContent());
    }

    public function test_kernel_dispatcher_returns_array(): void
    {
        $kernel = new HttpKernel(fn(Request $r) => ['ok' => true]);
        $res = $kernel->handle(Request::create('/'));
        $data = \json_decode($res->getContent(), true);
        $this->assertTrue($data['ok']);
    }

    public function test_kernel_middleware_short_circuits(): void
    {
        $kernel = new HttpKernel(fn() => Response::text('should not see this'));
        $kernel->addMiddleware(fn(Request $r) => Response::text('blocked'));
        $res = $kernel->handle(Request::create('/'));
        $this->assertSame('blocked', $res->getContent());
    }

    public function test_kernel_middleware_passes_through(): void
    {
        $kernel = new HttpKernel(fn() => Response::text('response'));
        $kernel->addMiddleware(fn(Request $r) => null);
        $res = $kernel->handle(Request::create('/'));
        $this->assertSame('response', $res->getContent());
    }

    public function test_kernel_terminate_callback(): void
    {
        $called = false;
        $kernel = new HttpKernel();
        $kernel->setTerminate(function (Request $r, Response $res) use (&$called) { $called = true; });
        $req = Request::create('/');
        $res = $kernel->handle($req);
        $kernel->terminate($req, $res);
        $this->assertTrue($called);
    }

    public function test_service_provider(): void
    {
        $provider = new HttpServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(HttpKernel::class, $provider->getKernel());
        $this->assertContains(HttpKernel::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/http.php';
        $this->assertArrayHasKey('trusted_proxies', $config);
        $this->assertArrayHasKey('middleware', $config);
    }
}
