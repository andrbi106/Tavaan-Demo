<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database;

/**
 * DatabaseServiceProvider — ثبت موتور Database.
 */
final class DatabaseServiceProvider
{
    private ?ConnectionManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->manager = new ConnectionManager($this->config);
    }

    public function boot(): void {}

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [ConnectionManager::class, Connection::class];
    }

    public function getManager(): ConnectionManager
    {
        if ($this->manager === null) $this->register();
        return $this->manager;
    }
}
