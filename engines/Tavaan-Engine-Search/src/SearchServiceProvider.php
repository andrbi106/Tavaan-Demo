<?php

declare(strict_types=1);

namespace Tavaan\Engine\Search;

/**
 * SearchServiceProvider — ثبت موتور Search.
 */
final class SearchServiceProvider
{
    private ?SearchManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->manager = new SearchManager($this->config);
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [SearchManager::class, Contracts\IndexInterface::class];
    }

    public function getManager(): SearchManager
    {
        if ($this->manager === null) {
            $this->register();
        }
        return $this->manager;
    }
}
