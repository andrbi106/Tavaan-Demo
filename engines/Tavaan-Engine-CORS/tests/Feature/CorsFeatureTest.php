<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Cors\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Cors\CorsMiddleware;
use Tavaan\Engine\Http\Cors\CorsServiceProvider;

final class CorsFeatureTest extends TestCase
{
    public function test_end_to_end_cors_workflow(): void
    {
        $provider = new CorsServiceProvider([
            'allowed_origins' => ['https://app.example.com'],
            'allowed_methods' => ['GET', 'POST'],
            'supports_credentials' => true,
        ]);
        $provider->register();
        $mw = $provider->getMiddleware();

        // Normal GET
        $result = $mw->handle([
            'method' => 'GET',
            'headers' => ['Origin' => 'https://app.example.com'],
        ], fn() => ['status' => 200, 'body' => '{"ok":true}', 'headers' => ['Content-Type' => 'application/json']]);
        $this->assertSame(200, $result['status']);
        $this->assertSame('https://app.example.com', $result['headers']['Access-Control-Allow-Origin']);
        $this->assertSame('true', $result['headers']['Access-Control-Allow-Credentials']);

        // Preflight
        $preflight = $mw->handle([
            'method' => 'OPTIONS',
            'headers' => ['Origin' => 'https://app.example.com', 'Access-Control-Request-Method' => 'POST'],
        ], fn() => []);
        $this->assertSame(204, $preflight['status']);
        $this->assertSame('GET, POST', $preflight['headers']['Access-Control-Allow-Methods']);
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/cors.php';
        $this->assertContains('*', $config['allowed_origins']);
    }
}
