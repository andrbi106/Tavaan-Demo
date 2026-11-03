<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy;

/**
 * TenantServiceProvider — ثبت موتور Tenancy.
 */
final class TenantServiceProvider
{
    private ?TenantManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $repo = new InMemoryTenantRepository();
        // Pre-create default tenants from config
        foreach ($this->config['default_tenants'] ?? [] as $data) {
            $repo->create($data);
        }
        $this->manager = new TenantManager($this->config, $repo);
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [TenantManager::class, Contracts\TenantRepositoryInterface::class];
    }

    public function getManager(): TenantManager
    {
        if ($this->manager === null) {
            $this->register();
        }
        return $this->manager;
    }
}
