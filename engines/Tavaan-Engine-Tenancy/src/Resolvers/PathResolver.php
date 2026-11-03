<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy\Resolvers;

use Tavaan\Engine\Tenancy\Contracts\TenantResolverInterface;

/**
 * PathResolver — استخراج tenant از URL path (مثلاً /{tenant}/...).
 */
final class PathResolver implements TenantResolverInterface
{
    public function __construct(
        private readonly int $segment = 1,
    ) {
    }

    public function resolve(array $request): ?string
    {
        $path = $request['path'] ?? '/';
        $parts = \explode('/', \trim($path, '/'));
        $index = $this->segment - 1;
        if (!isset($parts[$index]) || $parts[$index] === '') {
            return null;
        }
        return $parts[$index];
    }
}
