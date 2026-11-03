<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema;

/**
 * Blueprint — ساختار یک جدول با fluent API.
 *
 * @see docs/03-engines/schema.md
 */
final class Blueprint
{
    /** @var list<ColumnDefinition> */
    private array $columns = [];

    /** @var list<string> */
    private array $indexes = [];

    /** @var list<string> */
    private array $commands = [];

    public bool $isCreate = true;

    public function __construct(
        public readonly string $table,
    ) {
    }

    public function getTable(): string { return $this->table; }

    /** @return list<ColumnDefinition> */
    public function getColumns(): array { return $this->columns; }

    /** @return list<string> */
    public function getIndexes(): array { return $this->indexes; }

    public function isCreate(): bool { return $this->isCreate; }

    public function isAlter(): bool { return !$this->isCreate; }

    // === Column types ===

    public function id(string $name = 'id'): ColumnDefinition
    {
        return $this->addColumn($name, 'bigInteger')->unsigned()->autoIncrement()->primary();
    }

    public function integer(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'integer');
    }

    public function bigInteger(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'bigInteger');
    }

    public function unsignedBigInteger(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'bigInteger')->unsigned();
    }

    public function smallInteger(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'smallInteger');
    }

    public function tinyInteger(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'tinyInteger');
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        return $this->addColumn($name, 'string', length: $length);
    }

    public function text(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'text');
    }

    public function longText(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'longText');
    }

    public function boolean(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'boolean');
    }

    public function decimal(string $name, int $precision = 8, int $scale = 2): ColumnDefinition
    {
        return $this->addColumn($name, 'decimal', precision: $precision, scale: $scale);
    }

    public function double(string $name, ?int $precision = null, ?int $scale = null): ColumnDefinition
    {
        return $this->addColumn($name, 'double', precision: $precision, scale: $scale);
    }

    public function float(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'float');
    }

    public function date(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'date');
    }

    public function dateTime(string $name, ?int $precision = null): ColumnDefinition
    {
        return $this->addColumn($name, 'dateTime', precision: $precision);
    }

    public function timestamp(string $name, ?int $precision = null): ColumnDefinition
    {
        return $this->addColumn($name, 'timestamp', precision: $precision);
    }

    public function json(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'json');
    }

    public function jsonb(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'jsonb');
    }

    public function enum(string $name, array $values): ColumnDefinition
    {
        return $this->addColumn($name, 'enum')->enum($values);
    }

    public function blob(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'blob');
    }

    public function binary(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'binary');
    }

    public function ipAddress(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'ipAddress');
    }

    public function macAddress(string $name): ColumnDefinition
    {
        return $this->addColumn($name, 'macAddress');
    }

    public function uuid(string $name = 'uuid'): ColumnDefinition
    {
        return $this->addColumn($name, 'uuid');
    }

    public function ulid(string $name = 'ulid'): ColumnDefinition
    {
        return $this->addColumn($name, 'ulid');
    }

    // === Helpers ===

    public function timestamps(int $precision = 0): void
    {
        $this->timestamp('created_at', $precision ?: null)->nullable();
        $this->timestamp('updated_at', $precision ?: null)->nullable();
    }

    public function softDeletes(string $column = 'deleted_at'): void
    {
        $this->timestamp($column)->nullable();
    }

    public function rememberToken(): void
    {
        $this->string('remember_token', 100)->nullable();
    }

    public function index(array|string $columns, ?string $name = null): void
    {
        $cols = (array) $columns;
        $idxName = $name ?? $this->table . '_' . \implode('_', $cols) . '_index';
        $this->indexes[] = "INDEX {$idxName} (" . \implode(', ', $cols) . ')';
    }

    public function unique(array|string $columns, ?string $name = null): void
    {
        $cols = (array) $columns;
        $idxName = $name ?? $this->table . '_' . \implode('_', $cols) . '_unique';
        $this->indexes[] = "UNIQUE INDEX {$idxName} (" . \implode(', ', $cols) . ')';
    }

    /**
     * ALTER table — add column.
     */
    public function addColumn(string $name, string $type, ?int $length = null, ?int $precision = null, ?int $scale = null): ColumnDefinition
    {
        $col = new ColumnDefinition($name, $type, $length, $precision, $scale);
        $this->columns[] = $col;
        return $col;
    }

    public function dropColumn(string $name): void
    {
        $this->commands[] = "DROP COLUMN {$name}";
    }

    public function renameColumn(string $from, string $to): void
    {
        $this->commands[] = "RENAME COLUMN {$from} TO {$to}";
    }

    /** @return list<string> */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * all
     */
    public function all()
    {
        return [];
    }
    /**
     * column
     */
    public function column(string $name, string $type, ?int $length = null): ColumnDefinition
    {
        return $this->addColumn($name, $type, $length);
    }
    /**
     * foreign
     */
    public function foreign(string $column): self
    {
        $this->indexes[] = "FOREIGN KEY ({$column}) REFERENCES";
        return $this;
    }
    /**
     * fullText
     */
    public function fullText(array|string $columns, ?string $name = null): void
    {
        $cols = (array) $columns;
        $idxName = $name ?? $this->table . "_" . implode("_", $cols) . "_fulltext";
        $this->indexes[] = "FULLTEXT INDEX {$idxName} (" . implode(", ", $cols) . ")";
    }
    /**
     * primary
     */
    public function primary(array|string $columns = "id"): void
    {
        $cols = (array) $columns;
        $this->indexes[] = "PRIMARY KEY (" . implode(", ", $cols) . ")";
    }
    /**
     * types
     */
    public function types(): array
    {
        return array_unique(array_map(fn($col) => $col->type, $this->columns));
    }
}
