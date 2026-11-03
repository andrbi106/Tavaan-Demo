<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema;

/**
 * ColumnDefinition — تعریف یک ستون.
 *
 * @see docs/03-engines/schema.md (DOC-048)
 */
final class ColumnDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public ?int $length = null,
        public ?int $precision = null,
        public ?int $scale = null,
        public bool $nullable = false,
        public mixed $default = null,
        public bool $autoIncrement = false,
        public bool $unsigned = false,
        public bool $unique = false,
        public bool $primary = false,
        public ?array $enumValues = null,
        public ?string $after = null,
        public ?string $comment = null,
    ) {
    }

    public function nullable(bool $v = true): self
    {
        $c = clone $this; $c->nullable = $v; return $c;
    }

    public function default(mixed $v): self
    {
        $c = clone $this; $c->default = $v; return $c;
    }

    public function unique(): self
    {
        $c = clone $this; $c->unique = true; return $c;
    }

    public function primary(): self
    {
        $c = clone $this; $c->primary = true; return $c;
    }

    public function autoIncrement(): self
    {
        $c = clone $this; $c->autoIncrement = true; return $c;
    }

    public function unsigned(): self
    {
        $c = clone $this; $c->unsigned = true; return $c;
    }

    public function after(string $col): self
    {
        $c = clone $this; $c->after = $col; return $c;
    }

    public function comment(string $comment): self
    {
        $c = clone $this; $c->comment = $comment; return $c;
    }

    /**
     * @param list<string> $values
     */
    public function enum(array $values): self
    {
        $c = clone $this; $c->enumValues = $values; return $c;
    }
}
