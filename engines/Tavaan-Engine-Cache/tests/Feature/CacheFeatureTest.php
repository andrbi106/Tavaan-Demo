<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Cache\CacheManager;
use Tavaan\Engine\Cache\CacheRepository;
use Tavaan\Engine\Cache\CacheServiceProvider;
use Tavaan\Engine\Cache\Contracts\RepositoryInterface;

final class CacheFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new CacheServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(CacheManager::class, $provider->getManager());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new CacheServiceProvider([]);
        $this->assertContains(CacheManager::class, $provider->provides());
        $this->assertContains(RepositoryInterface::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/cache.php';
        $this->assertArrayHasKey('default', $config);
        $this->assertArrayHasKey('prefix', $config);
        $this->assertArrayHasKey('default_ttl', $config);
        $this->assertArrayHasKey('file_dir', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/cache.php';
        $this->assertArrayHasKey('error_miss', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/cache.php';
        $this->assertArrayHasKey('error_miss', $messages);
    }

    public function test_end_to_end_cache_operations(): void
    {
        $provider = new CacheServiceProvider(['default' => 'array']);
        $provider->register();
        $cache = $provider->getManager()->repository();
        $cache->put('user:1', ['name' => 'Ali'], 60);
        $this->assertSame(['name' => 'Ali'], $cache->get('user:1'));
        $this->assertTrue($cache->forget('user:1'));
        $this->assertFalse($cache->has('user:1'));
    }
}
