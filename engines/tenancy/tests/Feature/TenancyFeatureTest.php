<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Tenancy\Tenant;
use Tavaan\Engine\Tenancy\TenantManager;
use Tavaan\Engine\Tenancy\TenantContext;
use Tavaan\Engine\Tenancy\TenantServiceProvider;
use Tavaan\Engine\Tenancy\InMemoryTenantRepository;

/**
 * تست‌های Feature برای موتور Tenancy.
 */
final class TenancyFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new TenantServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(TenantManager::class, $provider->getManager());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new TenantServiceProvider([]);
        $this->assertContains(TenantManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/tenancy.php';
        $this->assertArrayHasKey('strategy', $config);
        $this->assertArrayHasKey('base_domain', $config);
        $this->assertArrayHasKey('resolvers', $config);
        $this->assertArrayHasKey('default_quota', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/tenancy.php';
        $this->assertArrayHasKey('error_not_found', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/tenancy.php';
        $this->assertArrayHasKey('error_not_found', $messages);
    }

    public function test_end_to_end_tenant_workflow(): void
    {
        $provider = new TenantServiceProvider([
            'default_tenants' => [
                ['identifier' => 'acme', 'plan' => 'pro', 'quota' => ['orders_per_day' => 1000]],
                ['identifier' => 'demo', 'plan' => 'free', 'quota' => ['orders_per_day' => 100]],
            ],
        ]);
        $provider->register();
        $m = $provider->getManager();

        // Find by identifier
        $acme = $m->findByIdentifier('acme');
        $this->assertSame('pro', $acme?->plan);

        // Set current and check quota
        $m->setCurrent($acme);
        $this->assertSame(1000, $m->quota('orders_per_day'));
        $this->assertSame('acme', $m->identifier());

        // Run within tenant context
        $demo = $m->findByIdentifier('demo');
        $result = $m->run($demo, fn() => $m->identifier());
        $this->assertSame('demo', $result);

        // After run, original tenant restored
        $this->assertSame('acme', $m->identifier());

        $m->clear();
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
    }
}
