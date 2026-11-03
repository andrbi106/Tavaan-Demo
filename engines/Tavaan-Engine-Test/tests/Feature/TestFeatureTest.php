<?php

declare(strict_types=1);

namespace Tavaan\Testing\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Testing\TestRunner;
use Tavaan\Testing\TestServiceProvider;
use Tavaan\Testing\TestCommand;

/**
 * تست‌های Feature برای موتور Test.
 */
final class TestFeatureTest extends TestCase
{
    public function test_service_provider_register_no_op(): void
    {
        $provider = new TestServiceProvider();
        $provider->register();
        $this->assertTrue(true);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new TestServiceProvider();
        $provides = $provider->provides();
        $this->assertContains(\Tavaan\Testing\TestCase::class, $provides);
        $this->assertContains(\Tavaan\Testing\TestRunner::class, $provides);
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/test.php';
        $this->assertArrayHasKey('mode', $config);
        $this->assertArrayHasKey('verbose', $config);
        $this->assertArrayHasKey('stop_on_failure', $config);
        $this->assertArrayHasKey('paths', $config);
        $this->assertArrayHasKey('unit', $config['paths']);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/test.php';
        $this->assertArrayHasKey('error_assertion_failed', $messages);
        $this->assertArrayHasKey('summary_pass', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/test.php';
        $this->assertArrayHasKey('error_assertion_failed', $messages);
        $this->assertArrayHasKey('summary_pass', $messages);
    }

    public function test_command_name_and_description(): void
    {
        $cmd = new TestCommand();
        $this->assertSame('test', $cmd->name());
        $this->assertStringContainsString('Tavaan', $cmd->description());
    }

    public function test_command_returns_1_when_no_dir(): void
    {
        $cmd = new TestCommand();
        $exit = $cmd->handle(['nonexistent_dir']);
        $this->assertSame(1, $exit);
    }

    public function test_command_runs_tests(): void
    {
        // Create temp tests dir with a simple test
        $tmpDir = \sys_get_temp_dir() . '/tvn_test_' . \uniqid();
        \mkdir($tmpDir . '/Unit', 0777, true);
        $testContent = <<<'PHP'
<?php
namespace TestTmp;
use Tavaan\Testing\TestCase;
class SimpleTest extends TestCase {
    public function testOne(): void {
        $this->assertTrue(true);
    }
    public function testTwo(): void {
        $this->assertSame(1, 1);
    }
}
PHP;
        \file_put_contents($tmpDir . '/Unit/SimpleTest.php', $testContent);

        $cmd = new TestCommand();
        $exit = $cmd->handle([$tmpDir]);
        $this->assertSame(0, $exit);

        // Clean up
        \unlink($tmpDir . '/Unit/SimpleTest.php');
        \rmdir($tmpDir . '/Unit');
        \rmdir($tmpDir);
    }
}
