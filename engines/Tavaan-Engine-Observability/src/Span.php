<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability;

/**
 * Span — single unit of work in a distributed trace.
 * Supports parent-chain (linked-list) for hierarchical spans.
 */
final class Span
{
    public readonly float $start;
    public ?float $end = null;
    public ?Span $parent = null;
    public string $spanId;
    public string $traceId;
    public string $status = 'ok';

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public readonly string $name,
        private array $attributes = [],
        ?Span $parent = null,
    ) {
        $this->start = microtime(true);
        $this->parent = $parent;
        $this->spanId = bin2hex(random_bytes(8));
        $this->traceId = $parent?->traceId ?? bin2hex(random_bytes(16));
    }

    public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /** @return array<string, mixed> */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function end(): void
    {
        if ($this->end === null) {
            $this->end = microtime(true);
        }
    }

    public function durationMs(): ?float
    {
        if ($this->end === null) { return null; }
        return ($this->end - $this->start) * 1000;
    }
}
