<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema;

use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\ConnectionManager;

/**
 * SchemaManager — facade برای SchemaBuilder با چند Connection.
 */
final class SchemaManager
{
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

    public function builder(?Connection $conn = null): SchemaBuilder
    {
        $c = $conn ?? $this->connectionManager?->connection() ?? new Connection('sqlite::memory:');
        return new SchemaBuilder($c);
    }

    public function create(string $table, callable $callback): void
    {
        $this->builder()->create($table, $callback);
    }

    public function table(string $table, callable $callback): void
    {
        $this->builder()->table($table, $callback);
    }

    public function drop(string $table): void
    {
        $this->builder()->drop($table);
    }

    public function dropIfExists(string $table): void
    {
        $this->builder()->dropIfExists($table);
    }

    public function rename(string $from, string $to): void
    {
        $this->builder()->rename($from, $to);
    }

    public function hasTable(string $table): bool
    {
        return $this->builder()->hasTable($table);
    }

    public function hasColumn(string $table, string $column): bool
    {
        return $this->builder()->hasColumn($table, $column);
    }

    /** @return list<string> */
    public function getTables(): array
    {
        return $this->builder()->getTables();
    }

    /** @return list<array{name: string, type: string}> */
    public function getColumns(string $table): array
    {
        return $this->builder()->getColumns($table);
    }

    public function getColumnType(string $table, string $column): ?string
    {
        return $this->builder()->getColumnType($table, $column);
    }
}
