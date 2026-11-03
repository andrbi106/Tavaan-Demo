<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy\Resolvers;

use Tavaan\Engine\Tenancy\Contracts\TenantResolverInterface;

/**
 * DomainResolver — استخراج tenant از دامنه (subdomain).
 */
final class DomainResolver implements TenantResolverInterface
{
    public function __construct(
        private readonly string $baseDomain = 'example.com',
    ) {
    }

    public function resolve(array $request): ?string
    {
        $host = $request['host'] ?? '';
        if (!\str_ends_with($host, ".{$this->baseDomain}")) {
            return null;
        }
        $subdomain = \substr($host, 0, -\strlen(".{$this->baseDomain}"));
        return $subdomain !== '' ? $subdomain : null;
    }
}
