<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy;

/**
 * Tenant — موجودیت یک tenant.
 */
final class Tenant
{
    public function __construct(
        public readonly int|string $id,
        public readonly string $identifier,
        public readonly string $plan = 'free',
        public readonly array $settings = [],
        public readonly array $quota = [],
    ) {
    }

    /**
     * @return mixed|null
     */
    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * @return mixed|null
     */
    public function quotaOf(string $resource): mixed
    {
        return $this->quota[$resource] ?? null;
    }
}
