<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability;

/**
 * TraceManager — manages spans (legacy API; use Tracer for newer code).
 */
final class TraceManager
{
    /** @var list<Span> */
    private array $spans = [];

    public function startSpan(string $name, array $attributes = []): Span
    {
        $span = new Span($name);
        foreach ($attributes as $k => $v) {
            $span->setAttribute($k, $v);
        }
        $this->spans[] = $span;
        return $span;
    }

    /** @return list<Span> */
    public function spans(): array
    {
        return $this->spans;
    }

    public function clear(): void
    {
        $this->spans = [];
    }

    /**
     * W3C traceparent header format.
     */
    public function traceparent(Span $span): string
    {
        return \sprintf('00-%s-%s-01', $span->traceId, $span->spanId);
    }
}
