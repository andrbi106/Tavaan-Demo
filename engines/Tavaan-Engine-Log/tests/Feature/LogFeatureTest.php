<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Log\LogManager;
use Tavaan\Engine\Log\Logger;
use Tavaan\Engine\Log\LogLevel;
use Tavaan\Engine\Log\LogServiceProvider;
use Tavaan\Engine\Log\Contracts\LoggerInterface;

/**
 * تست‌های Feature برای موتور Log.
 */
final class LogFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new LogServiceProvider([]);
        $provider->register();
        $manager = $provider->getManager();
        $this->assertInstanceOf(LogManager::class, $manager);
        $this->assertInstanceOf(LoggerInterface::class, $manager);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new LogServiceProvider([]);
        $this->assertContains(LoggerInterface::class, $provider->provides());
        $this->assertContains(LogManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/log.php';
        $this->assertArrayHasKey('level', $config);
        $this->assertArrayHasKey('default', $config);
        $this->assertArrayHasKey('handlers', $config);
        $this->assertArrayHasKey('mask_keys', $config);
        $this->assertArrayHasKey('rotation', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/log.php';
        $this->assertArrayHasKey('error_open_file', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/log.php';
        $this->assertArrayHasKey('error_open_file', $messages);
    }

    public function test_end_to_end_log_to_file(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn_log') . '.log';
        $manager = new LogManager([
            'handlers' => [
                ['type' => 'file', 'path' => $tmp, 'format' => 'json'],
            ],
        ]);
        $manager->info('test message', ['user' => 'ali']);
        $manager->flush();
        $content = \file_get_contents($tmp);
        $this->assertStringContainsString('test message', $content);
        $this->assertStringContainsString('INFO', $content);
        $this->assertStringContainsString('"user"', $content);
        @\unlink($tmp);
    }

    public function test_level_filtering(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn_log') . '.log';
        $manager = new LogManager([
            'level' => 'error',
            'handlers' => [
                ['type' => 'file', 'path' => $tmp, 'format' => 'json'],
            ],
        ]);
        $manager->debug('debug_msg');
        $manager->info('info_msg');
        $manager->warning('warning_msg');
        $manager->error('error_msg');
        $manager->flush();
        $content = \file_get_contents($tmp);
        $this->assertStringNotContainsString('debug_msg', $content);
        $this->assertStringNotContainsString('info_msg', $content);
        $this->assertStringNotContainsString('warning_msg', $content);
        $this->assertStringContainsString('error_msg', $content);
        @\unlink($tmp);
    }

    public function test_multiple_channels(): void
    {
        $tmp1 = \tempnam(\sys_get_temp_dir(), 'tvn_log1') . '.log';
        $tmp2 = \tempnam(\sys_get_temp_dir(), 'tvn_log2') . '.log';
        $manager = new LogManager([
            'handlers' => [
                ['type' => 'file', 'path' => $tmp1, 'format' => 'json'],
            ],
        ]);
        $audit = $manager->channel('audit');
        // Both should work
        $manager->info('app message');
        $audit->info('audit message');
        $manager->flush();
        $audit->flush();
        $content = \file_get_contents($tmp1);
        $this->assertStringContainsString('app message', $content);
        $this->assertStringContainsString('audit message', $content);
        $this->assertStringContainsString('"channel": "default"', $content);
        $this->assertStringContainsString('"channel": "audit"', $content);
        @\unlink($tmp1);
        @\unlink($tmp2);
    }
}
