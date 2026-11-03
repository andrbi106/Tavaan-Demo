<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy;

use Tavaan\Engine\Tenancy\Contracts\TenantRepositoryInterface;

/**
 * InMemoryTenantRepository — پیاده‌سازی in-memory برای تست.
 */
final class InMemoryTenantRepository implements TenantRepositoryInterface
{
    /** @var array<int|string, Tenant> */
    private array $tenants = [];

    /** @var array<string, int|string> */
    private array $byIdentifier = [];

    /** @var array<string, int|string> */
    private array $byDomain = [];

    public function find(int|string $id): ?Tenant
    {
        return $this->tenants[$id] ?? null;
    }

    public function findByIdentifier(string $identifier): ?Tenant
    {
        $id = $this->byIdentifier[$identifier] ?? null;
        return $id !== null ? $this->tenants[$id] : null;
    }

    public function findByDomain(string $domain): ?Tenant
    {
        $id = $this->byDomain[$domain] ?? null;
        return $id !== null ? $this->tenants[$id] : null;
    }

    public function create(array $data): Tenant
    {
        $id = $data['id'] ?? \count($this->tenants) + 1;
        $tenant = new Tenant(
            id: $id,
            identifier: $data['identifier'],
            plan: $data['plan'] ?? 'free',
            settings: $data['settings'] ?? [],
            quota: $data['quota'] ?? [],
        );
        $this->tenants[$id] = $tenant;
        $this->byIdentifier[$tenant->identifier] = $id;
        if (isset($data['domains']) && \is_array($data['domains'])) {
            foreach ($data['domains'] as $domain) {
                $this->byDomain[$domain] = $id;
            }
        }
        return $tenant;
    }

    public function update(int|string $id, array $data): ?Tenant
    {
        if (!isset($this->tenants[$id])) return null;
        $existing = $this->tenants[$id];
        $tenant = new Tenant(
            id: $id,
            identifier: $data['identifier'] ?? $existing->identifier,
            plan: $data['plan'] ?? $existing->plan,
            settings: $data['settings'] ?? $existing->settings,
            quota: $data['quota'] ?? $existing->quota,
        );
        $this->tenants[$id] = $tenant;
        return $tenant;
    }

    public function delete(int|string $id): bool
    {
        if (!isset($this->tenants[$id])) return false;
        $tenant = $this->tenants[$id];
        unset($this->byIdentifier[$tenant->identifier]);
        foreach ($this->byDomain as $domain => $tid) {
            if ($tid === $id) unset($this->byDomain[$domain]);
        }
        unset($this->tenants[$id]);
        return true;
    }

    public function count(): int
    {
        return \count($this->tenants);
    }
}
