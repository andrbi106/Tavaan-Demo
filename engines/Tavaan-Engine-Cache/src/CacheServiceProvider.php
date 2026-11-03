<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache;

/**
 * CacheServiceProvider — ثبت موتور Cache.
 */
final class CacheServiceProvider
{
    private ?CacheManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->manager = new CacheManager($this->config);
    }

    public function boot(): void {}

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [CacheManager::class, Contracts\RepositoryInterface::class];
    }

    public function getManager(): CacheManager
    {
        if ($this->manager === null) $this->register();
        return $this->manager;
    }
}
