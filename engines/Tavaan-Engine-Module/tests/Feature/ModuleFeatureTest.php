<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Module\Module;
use Tavaan\Engine\Module\ModuleRegistry;
use Tavaan\Engine\Module\ModuleServiceProvider;

/**
 * تست‌های Feature برای موتور Module.
 */
final class ModuleFeatureTest extends TestCase
{
    public function test_service_provider_creates_registry(): void
    {
        $provider = new ModuleServiceProvider(['path' => '/nonexistent']);
        $provider->register();
        $registry = $provider->getRegistry();
        $this->assertInstanceOf(ModuleRegistry::class, $registry);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new ModuleServiceProvider([]);
        $this->assertContains(ModuleRegistry::class, $provider->provides());
        $this->assertContains(Module::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/module.php';
        $this->assertArrayHasKey('path', $config);
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('disabled', $config);
        $this->assertArrayHasKey('hmvc', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/module.php';
        $this->assertArrayHasKey('error_not_found', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/module.php';
        $this->assertArrayHasKey('error_not_found', $messages);
    }

    public function test_end_to_end_register_and_query(): void
    {
        $provider = new ModuleServiceProvider(['path' => '/nonexistent']);
        $provider->register();
        $registry = $provider->getRegistry();
        $m = new \Tavaan\Engine\Module\Module(
            id: 'tavaan.shop',
            name: 'Shop',
            version: '1.0.0',
            path: '/tmp',
            manifest: ['id' => 'tavaan.shop', 'name' => 'Shop', 'version' => '1.0.0',
                'providers' => [], 'autoload' => [], 'requires' => ['tavaan.core-user']],
        );
        $registry->register($m);
        $this->assertTrue($registry->has('tavaan.shop'));
        $this->assertSame(['tavaan.core-user'], $registry->dependencies('tavaan.shop'));
    }
}
