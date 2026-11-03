<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Error\ErrorHandler;
use Tavaan\Engine\Error\HttpException;
use Tavaan\Engine\Error\ErrorServiceProvider;

/**
 * تست‌های Feature برای موتور Error.
 */
final class ErrorFeatureTest extends TestCase
{
    public function test_service_provider_creates_handler(): void
    {
        $provider = new ErrorServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(ErrorHandler::class, $provider->getHandler());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new ErrorServiceProvider([]);
        $this->assertContains(ErrorHandler::class, $provider->provides());
        $this->assertContains(HttpException::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/error.php';
        $this->assertArrayHasKey('debug', $config);
        $this->assertArrayHasKey('auto_register', $config);
        $this->assertArrayHasKey('strict_deprecated', $config);
        $this->assertArrayHasKey('sensitive_keys', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/error.php';
        $this->assertArrayHasKey('error_generic', $messages);
        $this->assertArrayHasKey('error_not_found', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/error.php';
        $this->assertArrayHasKey('error_generic', $messages);
        $this->assertArrayHasKey('error_not_found', $messages);
    }

    public function test_end_to_end_exception_handling(): void
    {
        $provider = new ErrorServiceProvider(['debug' => false]);
        $provider->register();
        $handler = $provider->getHandler();

        // Verify it can map exceptions
        $e = new \InvalidArgumentException('bad');
        $mapped = $handler->getMapper()->map($e);
        $this->assertSame(400, $mapped->statusCode);
        $this->assertSame('bad', $mapped->getMessage());
    }
}
