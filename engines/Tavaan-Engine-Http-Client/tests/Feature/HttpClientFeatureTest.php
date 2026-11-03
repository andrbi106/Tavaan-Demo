<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\HttpClient\HttpClient;
use Tavaan\Engine\HttpClient\HttpClientServiceProvider;
use Tavaan\Engine\HttpClient\Response;
use Tavaan\Engine\HttpClient\PendingRequest;

final class HttpClientFeatureTest extends TestCase
{
    public function test_service_provider_creates_client(): void
    {
        $provider = new HttpClientServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(HttpClient::class, $provider->getClient());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new HttpClientServiceProvider([]);
        $this->assertContains(HttpClient::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/http-client.php';
        $this->assertArrayHasKey('timeout', $config);
        $this->assertArrayHasKey('default_headers', $config);
        $this->assertArrayHasKey('retry', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/http-client.php';
        $this->assertArrayHasKey('error_request', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/http-client.php';
        $this->assertArrayHasKey('error_request', $messages);
    }

    public function test_end_to_end_request_building(): void
    {
        $provider = new HttpClientServiceProvider(['timeout' => 15]);
        $provider->register();
        $client = $provider->getClient();
        $req = $client->request()
            ->withHeaders(['Authorization' => 'Bearer test'])
            ->asJson()
            ->timeout(30)
            ->withQueryParameters(['page' => 1]);
        $this->assertSame('Bearer test', $req->getHeaders()['Authorization']);
        $this->assertSame('application/json', $req->getHeaders()['Content-Type']);
        $this->assertSame(30, $req->getTimeout());
        $this->assertSame(['page' => '1'], $req->getQuery());
    }
}
