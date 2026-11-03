<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Config\Repository;
use Tavaan\Engine\Config\EnvLoader;
use Tavaan\Engine\Config\EnvRepository;
use Tavaan\Engine\Config\ConfigLoader;
use Tavaan\Engine\Config\Exceptions\ConfigFrozenException;
use Tavaan\Engine\Config\Exceptions\ConfigException;
use Tavaan\Engine\Config\Exceptions\EnvParseException;
use Tavaan\Engine\Config\Contracts\RepositoryInterface;

/**
 * تست‌های Unit برای موتور Config.
 *
 * @see docs/03-engines/config.md (DOC-032)
 */
final class ConfigTest extends TestCase
{
    public function test_get_returns_default_when_missing(): void
    {
        $repo = new Repository();
        $this->assertSame('default', $repo->get('missing', 'default'));
    }

    public function test_get_returns_value_when_present(): void
    {
        $repo = new Repository(['name' => 'Tavaan']);
        $this->assertSame('Tavaan', $repo->get('name'));
    }

    public function test_dot_notation_get(): void
    {
        $repo = new Repository([
            'app' => ['name' => 'Tavaan', 'version' => '1.0'],
            'db' => ['host' => 'localhost'],
        ]);
        $this->assertSame('Tavaan', $repo->get('app.name'));
        $this->assertSame('1.0', $repo->get('app.version'));
        $this->assertSame('localhost', $repo->get('db.host'));
    }

    public function test_dot_notation_get_deep(): void
    {
        $repo = new Repository([
            'a' => ['b' => ['c' => ['d' => 'value']]],
        ]);
        $this->assertSame('value', $repo->get('a.b.c.d'));
    }

    public function test_dot_notation_get_returns_default_when_missing(): void
    {
        $repo = new Repository(['a' => ['b' => 'c']]);
        $this->assertSame('def', $repo->get('a.x.y', 'def'));
    }

    public function test_dot_notation_set(): void
    {
        $repo = new Repository([]);
        $repo->set('app.name', 'Tavaan');
        $this->assertSame(['app' => ['name' => 'Tavaan']], $repo->all());
    }

    public function test_dot_notation_set_deep(): void
    {
        $repo = new Repository([]);
        $repo->set('a.b.c.d', 'value');
        $this->assertSame('value', $repo->get('a.b.c.d'));
    }

    public function test_set_array_overrides(): void
    {
        $repo = new Repository(['old' => 'value']);
        $repo->set(['new' => 'value', 'other' => 'x']);
        $this->assertSame('value', $repo->get('new'));
        $this->assertSame('x', $repo->get('other'));
    }

    public function test_has(): void
    {
        $repo = new Repository(['app' => ['name' => 'Tavaan']]);
        $this->assertTrue($repo->has('app.name'));
        $this->assertFalse($repo->has('app.missing'));
        $this->assertFalse($repo->has('nonexistent'));
    }

    public function test_missing(): void
    {
        $repo = new Repository(['a' => 'b']);
        $this->assertTrue($repo->missing('x'));
        $this->assertFalse($repo->missing('a'));
    }

    public function test_forget(): void
    {
        $repo = new Repository(['a' => 'b', 'c' => 'd']);
        $repo->forget('a');
        $this->assertFalse($repo->has('a'));
        $this->assertTrue($repo->has('c'));
    }

    public function test_forget_dot_path(): void
    {
        $repo = new Repository(['app' => ['name' => 'Tavaan', 'env' => 'prod']]);
        $repo->forget('app.name');
        $this->assertFalse($repo->has('app.name'));
        $this->assertTrue($repo->has('app.env'));
    }

    public function test_pull(): void
    {
        $repo = new Repository(['key' => 'value']);
        $this->assertSame('value', $repo->pull('key'));
        $this->assertFalse($repo->has('key'));
    }

    public function test_pull_returns_default_when_missing(): void
    {
        $repo = new Repository([]);
        $this->assertSame('def', $repo->pull('missing', 'def'));
    }

    public function test_push_to_array(): void
    {
        $repo = new Repository(['items' => [1, 2]]);
        $repo->push('items', 3);
        $this->assertSame([1, 2, 3], $repo->get('items'));
    }

    public function test_push_creates_array_when_missing(): void
    {
        $repo = new Repository([]);
        $repo->push('items', 1);
        $this->assertSame([1], $repo->get('items'));
    }

    public function test_push_throws_on_non_array(): void
    {
        $repo = new Repository(['items' => 'string']);
        $this->expectException(ConfigException::class);
        $repo->push('items', 1);
    }

    public function test_prepend(): void
    {
        $repo = new Repository(['items' => [1, 2]]);
        $repo->prepend('items', 0);
        $this->assertSame([0, 1, 2], $repo->get('items'));
    }

    public function test_all(): void
    {
        $items = ['a' => 'b', 'c' => ['d' => 'e']];
        $repo = new Repository($items);
        $this->assertSame($items, $repo->all());
    }

    public function test_only(): void
    {
        $repo = new Repository(['a' => 1, 'b' => 2, 'c' => 3]);
        $result = $repo->only(['a', 'c']);
        $this->assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function test_except(): void
    {
        $repo = new Repository(['a' => 1, 'b' => 2, 'c' => 3]);
        $result = $repo->except(['b']);
        $this->assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function test_namespace(): void
    {
        $repo = new Repository([
            'app' => ['name' => 'Tavaan', 'env' => 'prod'],
            'db' => ['host' => 'localhost'],
        ]);
        $result = $repo->namespace('app');
        $this->assertSame(['name' => 'Tavaan', 'env' => 'prod'], $result);
    }

    public function test_freeze(): void
    {
        $repo = new Repository(['a' => 'b']);
        $repo->freeze();
        $this->assertTrue($repo->isFrozen());
        $this->expectException(ConfigFrozenException::class);
        $repo->set('c', 'd');
    }

    public function test_forget_after_freeze(): void
    {
        $repo = new Repository(['a' => 'b']);
        $repo->freeze();
        $this->expectException(ConfigFrozenException::class);
        $repo->forget('a');
    }

    public function test_required_keys_passes_when_present(): void
    {
        $repo = new Repository(['a' => 'b', 'c' => 'd']);
        $repo->requiredKeys(['a', 'c']);
        $this->assertTrue(true);
    }

    public function test_required_keys_throws_when_missing(): void
    {
        $repo = new Repository(['a' => 'b']);
        $this->expectException(ConfigException::class);
        $repo->requiredKeys(['a', 'missing']);
    }

    public function test_to_json_with_mask(): void
    {
        $repo = new Repository([
            'app_key' => 'secret_value',
            'name' => 'Tavaan',
            'db' => ['password' => 'p@ssw0rd'],
        ]);
        $json = $repo->toJson(true);
        $this->assertStringContainsString('***masked***', $json);
        $this->assertStringNotContainsString('secret_value', $json);
        $this->assertStringNotContainsString('p@ssw0rd', $json);
        $this->assertStringContainsString('Tavaan', $json);
    }

    public function test_to_json_without_mask(): void
    {
        $repo = new Repository(['api_key' => 'secret123']);
        $json = $repo->toJson(false);
        $this->assertStringContainsString('secret123', $json);
    }

    public function test_repository_interface_implemented(): void
    {
        $repo = new Repository([]);
        $this->assertInstanceOf(RepositoryInterface::class, $repo);
    }

    // === EnvLoader ===

    public function test_env_loader_parses_simple_file(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'env') . '.env';
        \file_put_contents($tmp, "APP_NAME=Tavaan\nAPP_DEBUG=true\n");
        $loader = new EnvLoader();
        $loader->load($tmp);
        $this->assertSame('Tavaan', $loader->get('APP_NAME'));
        $this->assertSame('true', $loader->get('APP_DEBUG'));
        @\unlink($tmp);
    }

    public function test_env_loader_handles_quoted_values(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'env') . '.env';
        \file_put_contents($tmp, "NAME=\"Hello World\"\nOTHER='single'\n");
        $loader = new EnvLoader();
        $loader->load($tmp);
        $this->assertSame('Hello World', $loader->get('NAME'));
        $this->assertSame('single', $loader->get('OTHER'));
        @\unlink($tmp);
    }

    public function test_env_loader_ignores_comments_and_blanks(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'env') . '.env';
        \file_put_contents($tmp, "# comment\n\nAPP_NAME=Tavaan\n");
        $loader = new EnvLoader();
        $loader->load($tmp);
        $this->assertSame('Tavaan', $loader->get('APP_NAME'));
        @\unlink($tmp);
    }

    public function test_env_loader_throws_on_invalid_line(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'env') . '.env';
        \file_put_contents($tmp, "INVALID_LINE\n");
        $loader = new EnvLoader();
        $this->expectException(EnvParseException::class);
        $loader->load($tmp);
        @\unlink($tmp);
    }

    public function test_env_loader_handles_missing_file(): void
    {
        $loader = new EnvLoader();
        $loader->load('/nonexistent.env');
        $this->assertEmpty($loader->all());
    }

    // === EnvRepository ===

    public function test_env_repository_get_returns_default(): void
    {
        $env = new EnvRepository();
        $this->assertNull($env->get('NONEXISTENT_VAR_XYZ'));
        $this->assertSame('default', $env->get('NONEXISTENT_VAR_XYZ', 'default'));
    }

    public function test_env_repository_string(): void
    {
        $env = new EnvRepository();
        $this->assertSame('def', $env->string('NONEXISTENT', 'def'));
    }

    public function test_env_repository_bool(): void
    {
        $env = new EnvRepository();
        $this->assertFalse($env->bool('NONEXISTENT', false));
        $this->assertTrue($env->bool('NONEXISTENT', true));
    }

    public function test_env_repository_int(): void
    {
        $env = new EnvRepository();
        $this->assertSame(42, $env->int('NONEXISTENT', 42));
    }

    public function test_env_repository_float(): void
    {
        $env = new EnvRepository();
        $this->assertSame(3.14, $env->float('NONEXISTENT', 3.14));
    }

    public function test_env_repository_list(): void
    {
        $env = new EnvRepository();
        $this->assertSame([], $env->list('NONEXISTENT'));
    }

    public function test_env_repository_json_returns_null_for_invalid(): void
    {
        $env = new EnvRepository();
        $this->assertNull($env->json('NONEXISTENT'));
    }

    public function test_env_repository_required_throws_when_missing(): void
    {
        $env = new EnvRepository();
        $this->expectException(\RuntimeException::class);
        $env->required(['NONEXISTENT_VAR_XYZ']);
    }

    // === ConfigLoader ===

    public function test_config_loader_loads_directory(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_config_' . \uniqid();
        \mkdir($tmpDir);
        \file_put_contents($tmpDir . '/app.php', '<?php return ["name" => "Tavaan"];');
        \file_put_contents($tmpDir . '/db.php', '<?php return ["host" => "localhost"];');

        $loader = new ConfigLoader($tmpDir);
        $config = $loader->load();
        $this->assertSame('Tavaan', $config['app']['name']);
        $this->assertSame('localhost', $config['db']['host']);

        \unlink($tmpDir . '/app.php');
        \unlink($tmpDir . '/db.php');
        \rmdir($tmpDir);
    }

    public function test_config_loader_load_specific_file(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_config_' . \uniqid();
        \mkdir($tmpDir);
        \file_put_contents($tmpDir . '/app.php', '<?php return ["name" => "Tavaan"];');

        $loader = new ConfigLoader($tmpDir);
        $config = $loader->loadFile('app');
        $this->assertSame(['name' => 'Tavaan'], $config);

        \unlink($tmpDir . '/app.php');
        \rmdir($tmpDir);
    }

    public function test_config_loader_merge_recursive(): void
    {
        $a = ['app' => ['name' => 'Tavaan', 'version' => '1.0']];
        $b = ['app' => ['version' => '2.0', 'env' => 'prod']];
        $merged = ConfigLoader::merge($a, $b);
        $this->assertSame([
            'app' => ['name' => 'Tavaan', 'version' => '2.0', 'env' => 'prod']
        ], $merged);
    }

    public function test_config_loader_returns_empty_when_dir_missing(): void
    {
        $loader = new ConfigLoader('/nonexistent_dir');
        $this->assertSame([], $loader->load());
    }
}
