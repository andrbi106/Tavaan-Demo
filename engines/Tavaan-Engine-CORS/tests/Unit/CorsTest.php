<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Cors\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Cors\CorsMiddleware;
use Tavaan\Engine\Http\Cors\CorsServiceProvider;

final class CorsTest extends TestCase
{
    public function test_preflight_returns_204_with_headers(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['*'], 'allowed_methods' => ['GET', 'POST']]);
        $result = $mw->handle([
            'method' => 'OPTIONS',
            'headers' => ['Origin' => 'https://example.com', 'Access-Control-Request-Method' => 'POST'],
        ], fn() => ['status' => 200, 'body' => '', 'headers' => []]);
        $this->assertSame(204, $result['status']);
        $this->assertSame('*', $result['headers']['Access-Control-Allow-Origin']);
        $this->assertSame('GET, POST', $result['headers']['Access-Control-Allow-Methods']);
    }

    public function test_preflight_blocked_origin(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['https://allowed.com']]);
        $result = $mw->handle([
            'method' => 'OPTIONS',
            'headers' => ['Origin' => 'https://evil.com', 'Access-Control-Request-Method' => 'POST'],
        ], fn() => []);
        $this->assertSame(403, $result['status']);
    }

    public function test_adds_cors_headers_on_normal_request(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['*']]);
        $result = $mw->handle([
            'method' => 'GET',
            'headers' => ['Origin' => 'https://example.com'],
        ], fn() => ['status' => 200, 'body' => 'ok', 'headers' => []]);
        $this->assertSame(200, $result['status']);
        $this->assertSame('*', $result['headers']['Access-Control-Allow-Origin']);
        $this->assertSame('Origin', $result['headers']['Vary']);
    }

    public function test_no_origin_skips_headers(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['*']]);
        $result = $mw->handle([
            'method' => 'GET',
            'headers' => [],
        ], fn() => ['status' => 200, 'body' => 'ok', 'headers' => []]);
        $this->assertArrayNotHasKey('Access-Control-Allow-Origin', $result['headers']);
    }

    public function test_reflects_origin_when_specific(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['https://example.com'], 'supports_credentials' => true]);
        $result = $mw->handle([
            'method' => 'GET',
            'headers' => ['Origin' => 'https://example.com'],
        ], fn() => ['status' => 200, 'body' => '', 'headers' => []]);
        $this->assertSame('https://example.com', $result['headers']['Access-Control-Allow-Origin']);
        $this->assertSame('true', $result['headers']['Access-Control-Allow-Credentials']);
    }

    public function test_wildcard_subdomain(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['*.example.com']]);
        $result = $mw->handle([
            'method' => 'GET',
            'headers' => ['Origin' => 'https://sub.example.com'],
        ], fn() => ['status' => 200, 'body' => '', 'headers' => []]);
        $this->assertSame('https://sub.example.com', $result['headers']['Access-Control-Allow-Origin']);
    }

    public function test_exposed_headers(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['*'], 'exposed_headers' => ['X-Total-Count', 'X-Page']]);
        $result = $mw->handle([
            'method' => 'GET',
            'headers' => ['Origin' => 'https://example.com'],
        ], fn() => ['status' => 200, 'body' => '', 'headers' => []]);
        $this->assertSame('X-Total-Count, X-Page', $result['headers']['Access-Control-Expose-Headers']);
    }

    public function test_preflight_max_age(): void
    {
        $mw = new CorsMiddleware(['allowed_origins' => ['*'], 'max_age' => 3600]);
        $result = $mw->handle([
            'method' => 'OPTIONS',
            'headers' => ['Origin' => 'https://example.com', 'Access-Control-Request-Method' => 'GET'],
        ], fn() => []);
        $this->assertSame('3600', $result['headers']['Access-Control-Max-Age']);
    }

    public function test_service_provider(): void
    {
        $provider = new CorsServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(CorsMiddleware::class, $provider->getMiddleware());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/cors.php';
        $this->assertArrayHasKey('allowed_origins', $config);
        $this->assertArrayHasKey('allowed_methods', $config);
    }
}
