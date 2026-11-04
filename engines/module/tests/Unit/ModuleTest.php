<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Module\Module;
use Tavaan\Engine\Module\ModuleRegistry;
use Tavaan\Engine\Module\ManifestParser;
use Tavaan\Engine\Module\TopologicalSorter;
use Tavaan\Engine\Module\Exceptions\ModuleException;

/**
 * تست‌های Unit برای موتور Module.
 *
 * @see docs/03-engines/module.md (DOC-038)
 */
final class ModuleTest extends TestCase
{
    public function test_module_construction(): void
    {
        $m = new Module(
            id: 'tavaan.shop',
            name: 'Shop',
            version: '1.0.0',
            path: '/path/to/shop',
            manifest: ['id' => 'tavaan.shop', 'name' => 'Shop', 'version' => '1.0.0',
                'providers' => ['ShopServiceProvider'], 'autoload' => [], 'requires' => ['tavaan.core-user']],
        );
        $this->assertSame('tavaan.shop', $m->id);
        $this->assertSame('Shop', $m->name);
        $this->assertSame('1.0.0', $m->version);
        $this->assertSame(['ShopServiceProvider'], $m->providers());
        $this->assertSame(['tavaan.core-user'], $m->requires());
    }

    public function test_module_from_array(): void
    {
        $m = Module::fromArray([
            'id' => 'tavaan.shop',
            'name' => 'Shop',
            'version' => '1.0.0',
            'providers' => ['ServiceProvider'],
            'autoload' => ['namespace-map' => ['Shop\\' => 'src/']],
        ], '/path/to/shop');
        $this->assertSame('tavaan.shop', $m->id);
        $this->assertSame('/path/to/shop', $m->path);
    }

    public function test_manifest_parser_valid(): void
    {
        $tmp = \sys_get_temp_dir() . '/tvn_module_' . \uniqid();
        \mkdir($tmp);
        $manifest = [
            'id' => 'tavaan.shop',
            'name' => 'Shop',
            'version' => '1.0.0',
            'providers' => ['ShopServiceProvider'],
            'autoload' => ['namespace-map' => ['Shop\\' => 'src/']],
        ];
        \file_put_contents($tmp . '/module.json', \json_encode($manifest));
        $parser = new ManifestParser();
        $m = $parser->parse($tmp . '/module.json');
        $this->assertSame('tavaan.shop', $m->id);
        \unlink($tmp . '/module.json');
        \rmdir($tmp);
    }

    public function test_manifest_parser_missing_file(): void
    {
        $parser = new ManifestParser();
        $this->expectException(ModuleException::class);
        $parser->parse('/nonexistent/module.json');
    }

    public function test_manifest_parser_invalid_json(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.json';
        \file_put_contents($tmp, 'not json');
        $parser = new ManifestParser();
        $this->expectException(ModuleException::class);
        $parser->parse($tmp);
        @\unlink($tmp);
    }

    public function test_manifest_parser_missing_required_field(): void
    {
        $tmp = \sys_get_temp_dir() . '/tvn_module_' . \uniqid();
        \mkdir($tmp);
        \file_put_contents($tmp . '/module.json', '{"id":"tavaan.shop","name":"Shop","version":"1.0.0"}');
        $parser = new ManifestParser();
        $this->expectException(ModuleException::class);
        $parser->parse($tmp . '/module.json');
        \unlink($tmp . '/module.json');
        \rmdir($tmp);
    }

    public function test_manifest_parser_invalid_id(): void
    {
        $tmp = \sys_get_temp_dir() . '/tvn_module_' . \uniqid();
        \mkdir($tmp);
        \file_put_contents($tmp . '/module.json', \json_encode([
            'id' => 'invalid_id',  // no dot
            'name' => 'Test', 'version' => '1.0.0',
            'providers' => ['P'], 'autoload' => [],
        ]));
        $parser = new ManifestParser();
        $this->expectException(ModuleException::class);
        $parser->parse($tmp . '/module.json');
        \unlink($tmp . '/module.json');
        \rmdir($tmp);
    }

    public function test_manifest_parser_invalid_version(): void
    {
        $tmp = \sys_get_temp_dir() . '/tvn_module_' . \uniqid();
        \mkdir($tmp);
        \file_put_contents($tmp . '/module.json', \json_encode([
            'id' => 'tavaan.shop',
            'name' => 'Test', 'version' => 'not_semver',
            'providers' => ['P'], 'autoload' => [],
        ]));
        $parser = new ManifestParser();
        $this->expectException(ModuleException::class);
        $parser->parse($tmp . '/module.json');
        \unlink($tmp . '/module.json');
        \rmdir($tmp);
    }

    public function test_topological_sorter_no_deps(): void
    {
        $sorter = new TopologicalSorter();
        $modules = [
            'a' => $this->makeModule('tavaan.a', []),
            'b' => $this->makeModule('tavaan.b', []),
        ];
        $sorted = $sorter->sort($modules);
        $this->assertCount(2, $sorted);
    }

    public function test_topological_sorter_with_deps(): void
    {
        $sorter = new TopologicalSorter();
        $modules = [
            'a' => $this->makeModule('tavaan.a', ['tavaan.b']),
            'b' => $this->makeModule('tavaan.b', []),
        ];
        $sorted = $sorter->sort($modules);
        // b must come before a (it's a dependency)
        $this->assertSame(['tavaan.b', 'tavaan.a'], $sorted);
    }

    public function test_topological_sorter_circular_dependency(): void
    {
        $sorter = new TopologicalSorter();
        $modules = [
            'a' => $this->makeModule('tavaan.a', ['tavaan.b']),
            'b' => $this->makeModule('tavaan.b', ['tavaan.a']),
        ];
        $this->expectException(ModuleException::class);
        $sorter->sort($modules);
    }

    public function test_registry_register_and_get(): void
    {
        $registry = new ModuleRegistry();
        $m = $this->makeModule('tavaan.shop', []);
        $registry->register($m);
        $this->assertSame($m, $registry->get('tavaan.shop'));
    }

    public function test_registry_get_not_found_throws(): void
    {
        $registry = new ModuleRegistry();
        $this->expectException(ModuleException::class);
        $registry->get('nonexistent');
    }

    public function test_registry_has(): void
    {
        $registry = new ModuleRegistry();
        $this->assertFalse($registry->has('tavaan.shop'));
        $registry->register($this->makeModule('tavaan.shop', []));
        $this->assertTrue($registry->has('tavaan.shop'));
    }

    public function test_registry_all(): void
    {
        $registry = new ModuleRegistry();
        $registry->register($this->makeModule('tavaan.a', []));
        $registry->register($this->makeModule('tavaan.b', []));
        $this->assertCount(2, $registry->all());
    }

    public function test_registry_enable_disable(): void
    {
        $registry = new ModuleRegistry();
        $m = $this->makeModule('tavaan.shop', []);
        $registry->register($m);
        $this->assertTrue($m->enabled);
        $registry->disable('tavaan.shop');
        $this->assertFalse($m->enabled);
        $registry->enable('tavaan.shop');
        $this->assertTrue($m->enabled);
    }

    public function test_registry_enabled_filter(): void
    {
        $registry = new ModuleRegistry();
        $enabled = $this->makeModule('tavaan.a', []);
        $disabled = $this->makeModule('tavaan.b', []);
        $disabled->enabled = false;
        $registry->register($enabled);
        $registry->register($disabled);
        $this->assertCount(1, $registry->enabled());
    }

    public function test_registry_sorted(): void
    {
        $registry = new ModuleRegistry();
        $registry->register($this->makeModule('tavaan.a', ['tavaan.b']));
        $registry->register($this->makeModule('tavaan.b', ['tavaan.c']));
        $registry->register($this->makeModule('tavaan.c', []));
        $sorted = $registry->sorted();
        $ids = \array_map(fn($m) => $m->id, $sorted);
        $this->assertSame(['tavaan.c', 'tavaan.b', 'tavaan.a'], $ids);
    }

    public function test_registry_dependencies(): void
    {
        $registry = new ModuleRegistry();
        $registry->register($this->makeModule('tavaan.shop', ['tavaan.core-user', 'tavaan.media']));
        $this->assertSame(['tavaan.core-user', 'tavaan.media'], $registry->dependencies('tavaan.shop'));
    }

    public function test_registry_dependents_of(): void
    {
        $registry = new ModuleRegistry();
        $registry->register($this->makeModule('tavaan.shop', ['tavaan.core-user']));
        $registry->register($this->makeModule('tavaan.media', ['tavaan.core-user']));
        $dependents = $registry->dependentsOf('tavaan.core-user');
        $this->assertContains('tavaan.shop', $dependents);
        $this->assertContains('tavaan.media', $dependents);
    }

    public function test_registry_discover(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_modules_' . \uniqid();
        \mkdir($tmpDir . '/shop', 0777, true);
        \file_put_contents($tmpDir . '/shop/module.json', \json_encode([
            'id' => 'tavaan.shop',
            'name' => 'Shop',
            'version' => '1.0.0',
            'providers' => ['P'],
            'autoload' => [],
        ]));
        $registry = new ModuleRegistry();
        $registry->discover($tmpDir);
        $this->assertTrue($registry->has('tavaan.shop'));
        \unlink($tmpDir . '/shop/module.json');
        \rmdir($tmpDir . '/shop');
        \rmdir($tmpDir);
    }

    private function makeModule(string $id, array $requires): Module
    {
        return new Module(
            id: $id,
            name: $id,
            version: '1.0.0',
            path: '/path/' . $id,
            manifest: [
                'id' => $id,
                'name' => $id,
                'version' => '1.0.0',
                'providers' => [],
                'autoload' => [],
                'requires' => $requires,
            ],
        );
    }
}
