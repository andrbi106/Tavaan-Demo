<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema;

use Tavaan\Engine\Database\ConnectionManager;

final class SchemaServiceProvider
{
    private ?SchemaManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
        private ?ConnectionManager $connectionManager = null,
    ) {
    }

    public function setConnectionManager(ConnectionManager $cm): self
    {
        $this->connectionManager = $cm;
        return $this;
    }

    public function register(): void
    {
        $this->manager = new SchemaManager($this->config);
        if ($this->connectionManager !== null) {
            $this->manager->setConnectionManager($this->connectionManager);
        }
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [SchemaManager::class, SchemaBuilder::class, Blueprint::class];
    }

    public function getManager(): SchemaManager
    {
        if ($this->manager === null) $this->register();
        return $this->manager;
    }
}
