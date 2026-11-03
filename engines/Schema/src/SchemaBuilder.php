<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema;

use Tavaan\Engine\Database\Connection;

/**
 * SchemaBuilder — اجرای Blueprint روی Connection.
 *
 * @see docs/03-engines/schema.md
 */
final class SchemaBuilder
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function create(string $table, callable $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $sql = $this->compileCreate($blueprint);
        $this->connection->exec($sql);
    }

    public function table(string $table, callable $callback): void
    {
        $blueprint = new Blueprint($table);
        $blueprint->isCreate = false;
        $callback($blueprint);
        $sql = $this->compileAlter($blueprint);
        if ($sql !== '') {
            $this->connection->exec($sql);
        }
    }

    public function drop(string $table): void
    {
        $this->connection->exec("DROP TABLE {$table}");
    }

    public function dropIfExists(string $table): void
    {
        $this->connection->exec("DROP TABLE IF EXISTS {$table}");
    }

    public function rename(string $from, string $to): void
    {
        $this->connection->exec("ALTER TABLE {$from} RENAME TO {$to}");
    }

    public function hasTable(string $table): bool
    {
        $rows = $this->connection->select(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name = ?",
            [$table]
        );
        return !empty($rows);
    }

    public function hasColumn(string $table, string $column): bool
    {
        try {
            $rows = $this->connection->select("PRAGMA table_info({$table})");
            foreach ($rows as $row) {
                if (($row['name'] ?? '') === $column) return true;
            }
        } catch (\Throwable) {}
        return false;
    }

    /**
     * @return list<string>
     */
    public function getTables(): array
    {
        $rows = $this->connection->select(
            "SELECT name FROM sqlite_master WHERE type = 'table' ORDER BY name"
        );
        return \array_map(fn($r) => $r['name'], $rows);
    }

    /**
     * @return list<array{name: string, type: string}>
     */
    public function getColumns(string $table): array
    {
        $rows = $this->connection->select("PRAGMA table_info({$table})");
        return \array_map(fn($r) => ['name' => $r['name'], 'type' => $r['type']], $rows);
    }

    public function getColumnType(string $table, string $column): ?string
    {
        foreach ($this->getColumns($table) as $col) {
            if ($col['name'] === $column) return $col['type'];
        }
        return null;
    }

    private function compileCreate(Blueprint $bp): string
    {
        $parts = [];
        foreach ($bp->getColumns() as $col) {
            $parts[] = $this->compileColumn($col);
        }
        foreach ($bp->getIndexes() as $idx) {
            $parts[] = $idx;
        }
        return "CREATE TABLE {$bp->getTable()} (
  " . \implode(",
  ", $parts) . "
)";
    }

    private function compileAlter(Blueprint $bp): string
    {
        $parts = [];
        foreach ($bp->getColumns() as $col) {
            $parts[] = "ADD COLUMN " . $this->compileColumn($col);
        }
        foreach ($bp->getCommands() as $cmd) {
            $parts[] = $cmd;
        }
        if (empty($parts)) return '';
        return "ALTER TABLE {$bp->getTable()} " . \implode(', ', $parts);
    }

    private function compileColumn(ColumnDefinition $col): string
    {
        $type = $this->mapType($col);
        $sql = "{$col->name} {$type}";
        if ($col->autoIncrement) $sql .= ' PRIMARY KEY AUTOINCREMENT';
        elseif ($col->primary) $sql .= ' PRIMARY KEY';
        if ($col->nullable) $sql .= ' NULL';
        else $sql .= ' NOT NULL';
        if ($col->default !== null) {
            $sql .= ' DEFAULT ' . $this->formatDefault($col->default);
        }
        if ($col->unique) $sql .= ' UNIQUE';
        if ($col->comment !== null) $sql .= " -- {$col->comment}";
        return $sql;
    }

    private function mapType(ColumnDefinition $col): string
    {
        return match ($col->type) {
            'integer', 'smallInteger', 'tinyInteger' => 'INTEGER',
            'bigInteger' => 'BIGINT' . ($col->unsigned ? ' UNSIGNED' : ''),
            'string' => 'VARCHAR(' . ($col->length ?? 255) . ')',
            'text', 'longText' => 'TEXT',
            'boolean' => 'BOOLEAN',
            'decimal' => "DECIMAL({$col->precision}, {$col->scale})",
            'double' => 'DOUBLE',
            'float' => 'REAL',
            'date' => 'DATE',
            'dateTime', 'timestamp' => 'DATETIME',
            'json', 'jsonb' => 'TEXT',  // SQLite stores JSON as TEXT
            'enum' => 'TEXT',  // SQLite doesn't have ENUM
            'blob', 'binary' => 'BLOB',
            'ipAddress', 'macAddress', 'uuid', 'ulid' => 'VARCHAR(255)',
            default => 'TEXT',
        };
    }

    private function formatDefault(mixed $value): string
    {
        if ($value === null) return 'NULL';
        if (\is_bool($value)) return $value ? '1' : '0';
        if (\is_int($value) || \is_float($value)) return (string) $value;
        if (\is_string($value)) return "'{$value}'";
        return 'NULL';
    }

    /**
     * introspect
     */
    public function introspect(string $table): array
    {
        return $this->getColumns($table);
    }
}
