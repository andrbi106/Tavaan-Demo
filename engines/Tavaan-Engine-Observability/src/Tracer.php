<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability;

/**
 * Tracer — minimal span-based tracer (supports parent/child span chains).
 */
final class Tracer
{
    /** @var list<Span> */
    private array $spans = [];
    private ?Span $current = null;

    /**
     * startSpan — begin a new span, attach as current.
     * @param array<string, mixed> $attributes
     */
    public function startSpan(string $name, array $attributes = []): Span
    {
        $span = new Span($name, $attributes, parent: $this->current);
        foreach ($attributes as $k => $v) {
            $span->setAttribute($k, $v);
        }
        $this->spans[] = $span;
        $this->current = $span;
        return $span;
    }

    /**
     * currentSpan — return the active span (or null if none).
     */
    public function currentSpan(): ?Span
    {
        return $this->current;
    }

    /**
     * endSpan — close the current span and return to its parent.
     */
    public function endSpan(): ?Span
    {
        if ($this->current === null) { return null; }
        $ended = $this->current;
        $ended->end();
        $this->current = $ended->parent;
        return $ended;
    }

    public function withSpan(): self
    {
        $clone = clone $this;
        return $clone;
    }

    /**
     * @return list<Span>
     */
    public function getSpans(): array
    {
        return $this->spans;
    }
}
