<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy;

use Tavaan\Engine\Tenancy\Contracts\TenantRepositoryInterface;

/**
 * TenantManager — مدیریت tenantهای موجود و context جاری.
 */
final class TenantManager
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
        private ?TenantRepositoryInterface $repository = null,
    ) {
    }

    public function setRepository(TenantRepositoryInterface $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    public function current(): ?Tenant
    {
        return TenantContext::current();
    }

    public function id(): int|string|null
    {
        return TenantContext::current()?->id;
    }

    public function identifier(): ?string
    {
        return TenantContext::current()?->identifier;
    }

    public function setCurrent(Tenant $tenant): self
    {
        TenantContext::setCurrent($tenant);
        return $this;
    }

    public function clear(): self
    {
        TenantContext::clear();
        return $this;
    }

    public function run(Tenant $tenant, \Closure $callback): mixed
    {
        return TenantContext::run($tenant, $callback);
    }

    public function withoutCurrent(\Closure $callback): mixed
    {
        return TenantContext::withoutCurrent($callback);
    }

    public function find(int|string $id): ?Tenant
    {
        return $this->repository?->find($id);
    }

    public function findByIdentifier(string $identifier): ?Tenant
    {
        return $this->repository?->findByIdentifier($identifier);
    }

    public function findByDomain(string $domain): ?Tenant
    {
        return $this->repository?->findByDomain($domain);
    }

    public function create(array $data): ?Tenant
    {
        return $this->repository?->create($data);
    }

    public function quota(string $resource): mixed
    {
        return TenantContext::current()?->quotaOf($resource);
    }
}
