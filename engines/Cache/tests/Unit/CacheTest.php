<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Cache\CacheRepository;
use Tavaan\Engine\Cache\Stores\ArrayStore;
use Tavaan\Engine\Cache\Stores\FileStore;
use Tavaan\Engine\Cache\Locks\CacheLock;
use Tavaan\Engine\Cache\Contracts\RepositoryInterface;

final class CacheTest extends TestCase
{
    public function test_get_returns_null_when_missing(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertNull($c->get('missing'));
        $this->assertSame('default', $c->get('missing', 'default'));
    }

    public function test_put_and_get(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k', 'v', 60);
        $this->assertSame('v', $c->get('k'));
    }

    public function test_has(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k', 'v', 60);
        $this->assertTrue($c->has('k'));
        $this->assertFalse($c->has('missing'));
    }

    public function test_missing(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertTrue($c->missing('missing'));
        $c->put('k', 'v', 60);
        $this->assertFalse($c->missing('k'));
    }

    public function test_forget(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k', 'v', 60);
        $this->assertTrue($c->forget('k'));
        $this->assertFalse($c->has('k'));
    }

    public function test_flush(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k1', 'v1', 60);
        $c->put('k2', 'v2', 60);
        $this->assertTrue($c->flush());
        $this->assertFalse($c->has('k1'));
        $this->assertFalse($c->has('k2'));
    }

    public function test_forever(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->forever('k', 'v');
        $this->assertSame('v', $c->get('k'));
    }

    public function test_remember_uses_existing(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k', 'existing', 60);
        $result = $c->remember('k', 60, fn() => 'computed');
        $this->assertSame('existing', $result);
    }

    public function test_remember_computes_when_missing(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $result = $c->remember('k', 60, fn() => 'computed');
        $this->assertSame('computed', $result);
        $this->assertSame('computed', $c->get('k'));
    }

    public function test_remember_forever(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $result = $c->rememberForever('k', fn() => 'v');
        $this->assertSame('v', $result);
        $this->assertSame('v', $c->get('k'));
    }

    public function test_add_returns_false_when_exists(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k', 'v', 60);
        $this->assertFalse($c->add('k', 'new', 60));
        $this->assertSame('v', $c->get('k'));
    }

    public function test_add_returns_true_when_new(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertTrue($c->add('k', 'v', 60));
        $this->assertSame('v', $c->get('k'));
    }

    public function test_pull(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k', 'v', 60);
        $this->assertSame('v', $c->pull('k'));
        $this->assertFalse($c->has('k'));
    }

    public function test_pull_default(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertSame('def', $c->pull('missing', 'def'));
    }

    public function test_increment(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertSame(1, $c->increment('c'));
        $this->assertSame(2, $c->increment('c'));
        $this->assertSame(5, $c->increment('c', 3));
    }

    public function test_decrement(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('c', 10, 60);
        $this->assertSame(9, $c->decrement('c'));
        $this->assertSame(7, $c->decrement('c', 2));
    }

    public function test_many(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->put('k1', 'v1', 60);
        $c->put('k2', 'v2', 60);
        $result = $c->many(['k1', 'k2', 'k3']);
        $this->assertSame(['k1' => 'v1', 'k2' => 'v2', 'k3' => null], $result);
    }

    public function test_put_many(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $c->putMany(['k1' => 'v1', 'k2' => 'v2'], 60);
        $this->assertSame('v1', $c->get('k1'));
        $this->assertSame('v2', $c->get('k2'));
    }

    public function test_file_store_put_and_get(): void
    {
        $dir = \sys_get_temp_dir() . '/tvn_cache_' . \uniqid();
        $fs = new FileStore($dir);
        $fs->put('k', 'v', 60);
        $this->assertSame('v', $fs->get('k'));
        \array_map('unlink', \glob($dir . '/*') ?: []);
        \rmdir($dir);
    }

    public function test_file_store_expires(): void
    {
        $dir = \sys_get_temp_dir() . '/tvn_cache_' . \uniqid();
        $fs = new FileStore($dir);
        $fs->put('k', 'v', 1);
        \sleep(2);
        $this->assertNull($fs->get('k'));
        \array_map('unlink', \glob($dir . '/*') ?: []);
        \rmdir($dir);
    }

    public function test_file_store_flush(): void
    {
        $dir = \sys_get_temp_dir() . '/tvn_cache_' . \uniqid();
        $fs = new FileStore($dir);
        $fs->put('k1', 'v1', 60);
        $fs->put('k2', 'v2', 60);
        $this->assertTrue($fs->flush());
        $this->assertFalse($fs->has('k1'));
        \rmdir($dir);
    }

    public function test_lock_acquire_and_release(): void
    {
        $lock = new CacheLock('test', 30);
        $this->assertTrue($lock->acquire());
        $this->assertFalse($lock->acquire());
        $this->assertTrue($lock->release());
        $this->assertTrue($lock->acquire());
        $lock->release();
    }

    public function test_lock_block_runs_callback(): void
    {
        $lock = new CacheLock('test', 30);
        $result = $lock->block(1, fn() => 'executed');
        $this->assertSame('executed', $result);
    }

    public function test_lock_owner_only_release(): void
    {
        $lock1 = new CacheLock('test', 30, 'owner-1');
        $lock2 = new CacheLock('test', 30, 'owner-2');
        $this->assertTrue($lock1->acquire());
        $this->assertFalse($lock2->release());  // different owner
        $this->assertTrue($lock1->release());  // correct owner
    }

    public function test_repository_implements_interface(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertInstanceOf(RepositoryInterface::class, $c);
    }

    public function test_get_default_driver(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertSame('array', $c->getDefaultDriver());
    }

    public function test_store_returns_same_for_default(): void
    {
        $c = new CacheRepository(['default' => 'array']);
        $this->assertSame($c, $c->store('array'));
        $this->assertSame($c, $c->store());
    }

    public function test_ttl_expiration(): void
    {
        $store = new ArrayStore();
        $store->put('k', 'v', 1);
        $this->assertSame('v', $store->get('k'));
        \sleep(2);
        $this->assertNull($store->get('k'));
    }
}
