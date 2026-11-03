<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM;

/**
 * ORMServiceProvider — ثبت موتور ORM.
 */
final class ORMServiceProvider
{
    private mixed $connectionResolver = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function setConnection(callable $resolver): self
    {
        $this->connectionResolver = $resolver;
        return $this;
    }

    public function register(): void
    {
        if ($this->connectionResolver !== null) {
            Model::setConnectionResolver($this->connectionResolver);
        }
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [Model::class, Collection::class, Repository::class];
    }
}
