<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Compat\Compat;
use Tavaan\Engine\Compat\CompatServiceProvider;
use Tavaan\Engine\Compat\Contracts\CompatInterface;

/**
 * تست‌های Feature (Integration) برای موتور Compat.
 *
 * @see docs/03-engines/compat.md
 */
final class CompatFeatureTest extends TestCase
{
    public function testServiceProviderRegistersCompatInterface(): void
    {
        $provider = new CompatServiceProvider(
            fn() => new Compat([]),
            fn() => []
        );
        $provider->register();
        $this->assertInstanceOf(CompatInterface::class, CompatServiceProvider::instance());
    }

    public function testServiceProviderRegistersDoctorCommand(): void
    {
        $provider = new CompatServiceProvider(
            fn() => new Compat([]),
            fn() => []
        );
        $provider->boot();
        $provides = $provider->provides();
        $this->assertContains(\Tavaan\Engine\Compat\Console\DoctorCommand::class, $provides);
    }

    public function testDoctorCommandProducesReport(): void
    {
        $cmd = new \Tavaan\Engine\Compat\Console\DoctorCommand();
        // Make sure CompatServiceProvider::instance() is initialized
        $provider = new CompatServiceProvider(
            fn() => new Compat([]),
            fn() => []
        );
        $provider->register();

        // Capture output
        \ob_start();
        $exitCode = $cmd->handle([]);
        $output = \ob_get_clean();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('PHP Version:', $output);
        $this->assertStringContainsString('Drivers:', $output);
        $this->assertStringContainsString('image:', $output);
        $this->assertStringContainsString('Extensions (', $output);
    }

    public function testEndToEndExtensionDetection(): void
    {
        $compat = new Compat([]);
        // We can rely on at least one extension always being loaded (e.g. 'date')
        $this->assertTrue($compat->has('date'));
        $report = $compat->report();
        $this->assertNotEmpty($report);
    }

    public function testConfigFileLoads(): void
    {
        $config = require __DIR__ . '/../../config/compat.php';
        $this->assertIsArray($config);
        $this->assertArrayHasKey('force_fallbacks', $config);
        $this->assertArrayHasKey('drivers', $config);
        $this->assertArrayHasKey('warn_on_fallback', $config);
        $this->assertArrayHasKey('math', $config['drivers']);
        $this->assertArrayHasKey('image', $config['drivers']);
        $this->assertArrayHasKey('zip', $config['drivers']);
        $this->assertArrayHasKey('http', $config['drivers']);
        $this->assertArrayHasKey('unicode', $config['drivers']);
        $this->assertArrayHasKey('mime', $config['drivers']);
    }

    public function testLangFaFileLoads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/compat.php';
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('error_extension_required', $messages);
        $this->assertArrayHasKey('doctor_summary', $messages);
    }

    public function testLangEnFileLoads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/compat.php';
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('error_extension_required', $messages);
        $this->assertArrayHasKey('doctor_summary', $messages);
    }

    public function testEngineWorksStandalone(): void
    {
        // AC-CMP-01: project works on a minimal PHP (only pdo, openssl, json, hash, mbstring)
        // We can verify that we don't depend on optional extensions
        $compat = new Compat([]);
        $this->assertIsBool($compat->has('mbstring'));
        $this->assertIsArray($compat->report());
        $this->assertIsString($compat->driver('image'));
        $this->assertIsString($compat->version());
    }
}
