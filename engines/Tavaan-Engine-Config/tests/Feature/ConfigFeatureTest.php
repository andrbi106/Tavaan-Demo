<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Config\Repository;
use Tavaan\Engine\Config\ConfigServiceProvider;
use Tavaan\Engine\Config\Contracts\RepositoryInterface;

/**
 * تست‌های Feature برای موتور Config.
 */
final class ConfigFeatureTest extends TestCase
{
    public function test_service_provider_creates_repository(): void
    {
        $provider = new ConfigServiceProvider();
        $provider->register();
        $repo = ConfigServiceProvider::getRepository();
        $this->assertInstanceOf(Repository::class, $repo);
        $this->assertInstanceOf(RepositoryInterface::class, $repo);
    }

    public function test_service_provider_creates_env_repository(): void
    {
        $provider = new ConfigServiceProvider();
        $provider->register();
        $env = ConfigServiceProvider::getEnvRepository();
        $this->assertNotNull($env->get('NONEXISTENT_XYZ', null));
        $this->assertNull($env->get('NONEXISTENT_XYZ', null));
    }

    public function test_service_provider_provides(): void
    {
        $provider = new ConfigServiceProvider();
        $provides = $provider->provides();
        $this->assertContains(RepositoryInterface::class, $provides);
        $this->assertContains(Repository::class, $provides);
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->assertIsArray($config);
        $this->assertArrayHasKey('env_file', $config);
        $this->assertArrayHasKey('cache', $config);
        $this->assertArrayHasKey('schema', $config);
        $this->assertArrayHasKey('frozen', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/config.php';
        $this->assertArrayHasKey('error_frozen', $messages);
        $this->assertArrayHasKey('error_missing_key', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/config.php';
        $this->assertArrayHasKey('error_frozen', $messages);
        $this->assertArrayHasKey('error_missing_key', $messages);
    }

    public function test_end_to_end_dot_notation(): void
    {
        $repo = new Repository([]);
        $repo->set('app.name', 'Tavaan');
        $repo->set('app.version', '1.0.0');
        $repo->set('db.host', 'localhost');
        $repo->set('db.port', 3306);

        $this->assertSame('Tavaan', $repo->get('app.name'));
        $this->assertSame('1.0.0', $repo->get('app.version'));
        $this->assertSame('localhost', $repo->get('db.host'));
        $this->assertSame(3306, $repo->get('db.port'));
        $this->assertSame(['name' => 'Tavaan', 'version' => '1.0.0'], $repo->namespace('app'));
    }

    public function test_freeze_then_reject_writes(): void
    {
        $repo = new Repository(['a' => 'b']);
        $repo->freeze();
        try {
            $repo->set('c', 'd');
            $this->fail('Expected ConfigFrozenException');
        } catch (\Tavaan\Engine\Config\Exceptions\ConfigFrozenException $e) {
            $this->assertTrue(true);
        }
    }
}
