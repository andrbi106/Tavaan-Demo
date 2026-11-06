<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM;

/**
 * Collection — مجموعه‌ای از Modelها با متدهای کمکی.
 *
 * @template T of Model
 */
final class Collection
{
    /**
     * @param list<Model> $items
     */
    public function __construct(
        private array $items = [],
    ) {
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function first(): ?Model
    {
        return $this->items[0] ?? null;
    }

    public function last(): ?Model
    {
        return $this->items[\count($this->items) - 1] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @return list<Model>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function pluck(string $key): array
    {
        return \array_map(fn($m) => $m->getAttribute($key), $this->items);
    }

    public function keyBy(string $key): array
    {
        $result = [];
        foreach ($this->items as $m) {
            $k = $m->getAttribute($key);
            if ($k !== null) $result[$k] = $m;
        }
        return $result;
    }

    public function where(string $key, mixed $value): self
    {
        return new self(\array_filter($this->items, fn($m) => $m->getAttribute($key) === $value));
    }

    public function each(callable $callback): self
    {
        foreach ($this->items as $item) $callback($item);
        return $this;
    }

    public function map(callable $callback): array
    {
        return \array_map($callback, $this->items);
    }

    public function filter(callable $callback): self
    {
        return new self(\array_filter($this->items, $callback));
    }

    public function sortBy(string $key, string $direction = 'asc'): self
    {
        $items = $this->items;
        \usort($items, function ($a, $b) use ($key, $direction) {
            $av = $a->getAttribute($key);
            $bv = $b->getAttribute($key);
            $cmp = $av <=> $bv;
            return $direction === 'desc' ? -$cmp : $cmp;
        });
        return new self($items);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function toArray(): array
    {
        return \array_map(fn($m) => $m->toArray(), $this->items);
    }
}
