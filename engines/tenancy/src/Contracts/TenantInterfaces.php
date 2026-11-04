<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy\Contracts;

use Tavaan\Engine\Tenancy\Tenant;

/**
 * قرارداد Tenant Resolver — استخراج tenant از request.
 */
interface TenantResolverInterface
{
    public function resolve(array $request): ?string;
}

/**
 * قرارداد Tenant Repository — ذخیره/جست‌وجوی tenantها.
 */
interface TenantRepositoryInterface
{
    public function find(int|string $id): ?Tenant;

    public function findByIdentifier(string $identifier): ?Tenant;

    public function findByDomain(string $domain): ?Tenant;

    public function create(array $data): Tenant;

    public function update(int|string $id, array $data): ?Tenant;

    public function delete(int|string $id): bool;
}
