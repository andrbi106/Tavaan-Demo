<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Console\Application;
use Tavaan\Engine\Console\ConsoleServiceProvider;

/**
 * تست‌های Feature برای موتور Console.
 */
final class ConsoleFeatureTest extends TestCase
{
    public function test_service_provider_creates_application(): void
    {
        $provider = new ConsoleServiceProvider([]);
        $provider->register();
        $app = $provider->getApplication();
        $this->assertInstanceOf(Application::class, $app);
        // 'list' command should be auto-registered
        $this->assertNotNull($app->get('list'));
    }

    public function test_service_provider_provides(): void
    {
        $provider = new ConsoleServiceProvider([]);
        $this->assertContains(Application::class, $provider->provides());
        $this->assertContains(\Tavaan\Engine\Console\Command::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/console.php';
        $this->assertArrayHasKey('paths', $config);
        $this->assertArrayHasKey('colors', $config);
        $this->assertArrayHasKey('verbose', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/console.php';
        $this->assertArrayHasKey('error_command_not_found', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/console.php';
        $this->assertArrayHasKey('error_command_not_found', $messages);
    }

    public function test_end_to_end_command_execution(): void
    {
        $provider = new ConsoleServiceProvider([]);
        $provider->register();
        $app = $provider->getApplication();
        $app->add(new \Tavaan\Engine\Console\Tests\Unit\TestCommand());
        \ob_start();
        $exit = $app->run(['test:cmd']);
        $output = \ob_get_clean();
        $this->assertSame(0, $exit);
        $this->assertStringContainsString('test command executed', $output);
    }
}
