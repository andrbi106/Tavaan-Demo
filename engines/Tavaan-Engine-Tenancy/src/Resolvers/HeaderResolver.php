<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy\Resolvers;

use Tavaan\Engine\Tenancy\Contracts\TenantResolverInterface;

/**
 * HeaderResolver — استخراج tenant از header HTTP (مثلاً X-Tenant-ID).
 */
final class HeaderResolver implements TenantResolverInterface
{
    public function __construct(
        private readonly string $headerName = 'X-Tenant-ID',
    ) {
    }

    public function resolve(array $request): ?string
    {
        $headers = $request['headers'] ?? [];
        // Case-insensitive header lookup
        foreach ($headers as $name => $value) {
            if (\strtolower($name) === \strtolower($this->headerName)) {
                return $value !== '' ? $value : null;
            }
        }
        return null;
    }
}
