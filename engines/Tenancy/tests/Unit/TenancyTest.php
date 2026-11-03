<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Tenancy\Tenant;
use Tavaan\Engine\Tenancy\TenantContext;
use Tavaan\Engine\Tenancy\TenantManager;
use Tavaan\Engine\Tenancy\TenantResolveChain;
use Tavaan\Engine\Tenancy\InMemoryTenantRepository;
use Tavaan\Engine\Tenancy\Resolvers\DomainResolver;
use Tavaan\Engine\Tenancy\Resolvers\HeaderResolver;
use Tavaan\Engine\Tenancy\Resolvers\PathResolver;

/**
 * تست‌های Unit برای موتور Tenancy.
 *
 * @see docs/03-engines/tenancy.md (DOC-082E)
 */
final class TenancyTest extends TestCase
{
    public function test_tenant_construction(): void
    {
        $t = new Tenant(1, 'acme', 'pro', ['theme' => 'dark'], ['orders_per_day' => 1000]);
        $this->assertSame(1, $t->id);
        $this->assertSame('acme', $t->identifier);
        $this->assertSame('pro', $t->plan);
        $this->assertSame(['theme' => 'dark'], $t->settings);
        $this->assertSame(1000, $t->quotaOf('orders_per_day'));
        $this->assertNull($t->quotaOf('nonexistent'));
    }

    public function test_tenant_setting(): void
    {
        $t = new Tenant(1, 'acme', settings: ['theme' => 'light']);
        $this->assertSame('light', $t->setting('theme'));
        $this->assertSame('default', $t->setting('missing', 'default'));
    }

    public function test_tenant_context_set_clear(): void
    {
        $t = new Tenant(1, 'acme');
        $this->assertFalse(TenantContext::isSet());
        TenantContext::setCurrent($t);
        $this->assertTrue(TenantContext::isSet());
        $this->assertSame($t, TenantContext::current());
        TenantContext::clear();
        $this->assertNull(TenantContext::current());
        $this->assertFalse(TenantContext::isSet());
    }

    public function test_tenant_context_run(): void
    {
        $t = new Tenant(1, 'acme');
        $result = TenantContext::run($t, function () {
            return TenantContext::current()?->identifier;
        });
        $this->assertSame('acme', $result);
        $this->assertNull(TenantContext::current());
    }

    public function test_tenant_context_run_restores_previous(): void
    {
        $outer = new Tenant(1, 'outer');
        TenantContext::setCurrent($outer);
        $inner = new Tenant(2, 'inner');
        TenantContext::run($inner, function () use ($inner) {
            $this->assertSame($inner, TenantContext::current());
        });
        $this->assertSame($outer, TenantContext::current());
        TenantContext::clear();
    }

    public function test_tenant_context_without_current(): void
    {
        $t = new Tenant(1, 'acme');
        TenantContext::setCurrent($t);
        $result = TenantContext::withoutCurrent(function () {
            return TenantContext::current();
        });
        $this->assertNull($result);
        $this->assertSame($t, TenantContext::current());
        TenantContext::clear();
    }

    public function test_domain_resolver(): void
    {
        $r = new DomainResolver('example.com');
        $this->assertSame('acme', $r->resolve(['host' => 'acme.example.com']));
        $this->assertSame('tenant1', $r->resolve(['host' => 'tenant1.example.com']));
        $this->assertNull($r->resolve(['host' => 'example.com']));
        $this->assertNull($r->resolve(['host' => 'other.org']));
    }

    public function test_header_resolver(): void
    {
        $r = new HeaderResolver('X-Tenant-ID');
        $this->assertSame('acme', $r->resolve(['headers' => ['X-Tenant-ID' => 'acme']]));
        $this->assertSame('tenant1', $r->resolve(['headers' => ['x-tenant-id' => 'tenant1']]));  // case insensitive
        $this->assertNull($r->resolve(['headers' => ['Other' => 'acme']]));
        $this->assertNull($r->resolve(['headers' => []]));
    }

    public function test_path_resolver(): void
    {
        $r = new PathResolver(1);
        $this->assertSame('acme', $r->resolve(['path' => '/acme/dashboard']));
        $this->assertSame('acme', $r->resolve(['path' => '/acme']));
        $this->assertNull($r->resolve(['path' => '/']));
        $this->assertNull($r->resolve(['path' => '']));
    }

    public function test_path_resolver_segment_2(): void
    {
        $r = new PathResolver(2);
        $this->assertSame('tenant', $r->resolve(['path' => '/api/tenant/list']));
    }

    public function test_resolve_chain(): void
    {
        $chain = new TenantResolveChain([
            new DomainResolver('example.com'),
            new HeaderResolver('X-Tenant-ID'),
            new PathResolver(1),
        ]);
        $this->assertSame('acme', $chain->resolve(['host' => 'acme.example.com']));
        $this->assertSame('from-header', $chain->resolve(['headers' => ['X-Tenant-ID' => 'from-header']]));
        $this->assertSame('from-path', $chain->resolve(['path' => '/from-path/list']));
        $this->assertNull($chain->resolve([]));
    }

    public function test_resolve_chain_priority_domain_first(): void
    {
        $chain = new TenantResolveChain([
            new DomainResolver('example.com'),
            new HeaderResolver('X-Tenant-ID'),
        ]);
        // Both have values, domain wins (first in chain)
        $result = $chain->resolve([
            'host' => 'domain-tenant.example.com',
            'headers' => ['X-Tenant-ID' => 'header-tenant'],
        ]);
        $this->assertSame('domain-tenant', $result);
    }

    public function test_in_memory_repo_create_and_find(): void
    {
        $repo = new InMemoryTenantRepository();
        $tenant = $repo->create(['identifier' => 'acme', 'plan' => 'pro']);
        $this->assertSame('acme', $tenant->identifier);
        $found = $repo->find($tenant->id);
        $this->assertSame($tenant, $found);
    }

    public function test_in_memory_repo_find_by_identifier(): void
    {
        $repo = new InMemoryTenantRepository();
        $repo->create(['identifier' => 'acme']);
        $repo->create(['identifier' => 'other']);
        $this->assertSame('acme', $repo->findByIdentifier('acme')?->identifier);
        $this->assertSame('other', $repo->findByIdentifier('other')?->identifier);
        $this->assertNull($repo->findByIdentifier('nonexistent'));
    }

    public function test_in_memory_repo_find_by_domain(): void
    {
        $repo = new InMemoryTenantRepository();
        $repo->create(['identifier' => 'acme', 'domains' => ['acme.example.com', 'acme.io']]);
        $this->assertSame('acme', $repo->findByDomain('acme.example.com')?->identifier);
        $this->assertSame('acme', $repo->findByDomain('acme.io')?->identifier);
        $this->assertNull($repo->findByDomain('other.com'));
    }

    public function test_in_memory_repo_update(): void
    {
        $repo = new InMemoryTenantRepository();
        $t = $repo->create(['identifier' => 'acme', 'plan' => 'free']);
        $updated = $repo->update($t->id, ['plan' => 'pro']);
        $this->assertSame('pro', $updated?->plan);
        $this->assertSame('acme', $updated?->identifier);
    }

    public function test_in_memory_repo_delete(): void
    {
        $repo = new InMemoryTenantRepository();
        $t = $repo->create(['identifier' => 'acme']);
        $this->assertTrue($repo->delete($t->id));
        $this->assertNull($repo->find($t->id));
        $this->assertFalse($repo->delete(999));
    }

    public function test_in_memory_repo_count(): void
    {
        $repo = new InMemoryTenantRepository();
        $this->assertSame(0, $repo->count());
        $repo->create(['identifier' => 'a']);
        $repo->create(['identifier' => 'b']);
        $this->assertSame(2, $repo->count());
    }

    public function test_manager_set_current(): void
    {
        $m = new TenantManager();
        $t = new Tenant(1, 'acme');
        $m->setCurrent($t);
        $this->assertSame($t, $m->current());
        $this->assertSame(1, $m->id());
        $this->assertSame('acme', $m->identifier());
        $m->clear();
        $this->assertNull($m->current());
    }

    public function test_manager_run(): void
    {
        $m = new TenantManager();
        $t = new Tenant(1, 'acme');
        $result = $m->run($t, fn() => $m->identifier());
        $this->assertSame('acme', $result);
        $this->assertNull($m->current());
    }

    public function test_manager_without_current(): void
    {
        $m = new TenantManager();
        $t = new Tenant(1, 'acme');
        $m->setCurrent($t);
        $result = $m->withoutCurrent(fn() => $m->current());
        $this->assertNull($result);
        $this->assertSame($t, $m->current());
        $m->clear();
    }

    public function test_manager_quota(): void
    {
        $m = new TenantManager();
        $t = new Tenant(1, 'acme', quota: ['orders_per_day' => 1000]);
        $m->setCurrent($t);
        $this->assertSame(1000, $m->quota('orders_per_day'));
        $this->assertNull($m->quota('unknown'));
        $m->clear();
    }

    public function test_manager_find_with_repo(): void
    {
        $repo = new InMemoryTenantRepository();
        $repo->create(['identifier' => 'acme']);
        $m = new TenantManager([], $repo);
        $found = $m->findByIdentifier('acme');
        $this->assertSame('acme', $found?->identifier);
    }

    public function test_manager_create(): void
    {
        $repo = new InMemoryTenantRepository();
        $m = new TenantManager([], $repo);
        $t = $m->create(['identifier' => 'new-tenant', 'plan' => 'pro']);
        $this->assertSame('new-tenant', $t?->identifier);
        $this->assertSame('pro', $t?->plan);
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
    }
}
