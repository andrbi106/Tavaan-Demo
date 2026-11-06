<?php
declare(strict_types=1);
namespace Tavaan\Core\Context;

/**
 * Context ردیابی (tracing) — شناسهٔ request، span، trace.
 * برای observability و distributed tracing.
 */
final class TraceContext
{
    public function __construct(
        private ?string $traceId = null,
        private ?string $spanId = null,
        private ?string $parentId = null,
    ) {
        if ($traceId === null) {
            $this->traceId = self::generateId(16);
        }
        if ($spanId === null) {
            $this->spanId = self::generateId(8);
        }
    }

    public function traceId(): string { return $this->traceId; }
    public function spanId(): string { return $this->spanId; }
    public function parentId(): ?string { return $this->parentId; }

    public function withSpan(string $spanId, ?string $parentId = null): self
    {
        return new self($this->traceId, $spanId, $parentId ?? $this->spanId);
    }

    private static function generateId(int $length): string
    {
        return bin2hex(random_bytes($length));
    }
}
