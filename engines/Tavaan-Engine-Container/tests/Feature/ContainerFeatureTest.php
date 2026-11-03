<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Container\Container;
use Tavaan\Engine\Container\ContainerServiceProvider;
use Tavaan\Engine\Container\Contracts\ContainerInterface;

/**
 * تست‌های Feature برای موتور Container.
 */
final class ContainerFeatureTest extends TestCase
{
    public function testServiceProviderRegistersContainer(): void
    {
        $container = new Container();
        $provider = new ContainerServiceProvider($container);
        $provider->register();
        $this->assertSame($container, $container->make(ContainerInterface::class));
    }

    public function testServiceProviderProvides(): void
    {
        $container = new Container();
        $provider = new ContainerServiceProvider($container);
        $this->assertContains(ContainerInterface::class, $provider->provides());
        $this->assertContains(Container::class, $provider->provides());
    }

    public function testConfigFileLoads(): void
    {
        $config = require __DIR__ . '/../../config/container.php';
        $this->assertIsArray($config);
        $this->assertArrayHasKey('reflection_cache', $config);
        $this->assertArrayHasKey('max_depth', $config);
        $this->assertArrayHasKey('strict', $config);
        $this->assertArrayHasKey('auto_build', $config);
    }

    public function testLangFaFileLoads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/container.php';
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('error_no_binding', $messages);
        $this->assertArrayHasKey('error_cyclic', $messages);
    }

    public function testLangEnFileLoads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/container.php';
        $this->assertIsArray($messages);
        $this->assertArrayHasKey('error_no_binding', $messages);
        $this->assertArrayHasKey('error_cyclic', $messages);
    }

    public function testEndToEndResolveWithAutoWiring(): void
    {
        $container = new Container();
        $obj = $container->make(\Tavaan\Engine\Container\Tests\Unit\SimpleClass::class);
        $this->assertInstanceOf(\Tavaan\Engine\Container\Tests\Unit\SimpleClass::class, $obj);
    }
}
