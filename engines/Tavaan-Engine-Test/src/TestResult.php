<?php

declare(strict_types=1);

namespace Tavaan\Testing;

/**
 * نتیجهٔ اجرای یک تست.
 */
final class TestResult
{
    public function __construct(
        public readonly string $name,
        public readonly string $className,
        public ?bool $passed = null,
        public ?string $failureMessage = null,
        public float $durationMs = 0.0,
    ) {
    }

    public function pass(): void
    {
        $this->passed = true;
    }

    public function fail(string $message): void
    {
        $this->passed = false;
        $this->failureMessage = $message;
    }

    public function isPassed(): bool
    {
        return $this->passed === true;
    }

    public function __toString(): string
    {
        $status = $this->isPassed() ? '✅' : '❌';
        $msg = $this->failureMessage ? " | {$this->failureMessage}" : '';
        return "{$status} {$this->className}::{$this->name}{$msg}";
    }
}
