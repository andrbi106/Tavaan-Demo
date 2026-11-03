<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy;

use Tavaan\Engine\Tenancy\Contracts\TenantResolverInterface;

/**
 * TenantResolveChain — chain of resolvers (Domain → Header → Path → Token → Query → Default).
 */
final class TenantResolveChain implements TenantResolverInterface
{
    /**
     * @param list<TenantResolverInterface> $resolvers
     */
    public function __construct(
        private readonly array $resolvers = [],
    ) {
    }

    public function resolve(array $request): ?string
    {
        foreach ($this->resolvers as $resolver) {
            $id = $resolver->resolve($request);
            if ($id !== null) {
                return $id;
            }
        }
        return null;
    }

    public function addResolver(TenantResolverInterface $resolver): self
    {
        $this->resolvers[] = $resolver;
        return $this;
    }
}
