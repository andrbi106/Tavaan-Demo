<?php

declare(strict_types=1);

namespace Tavaan\Testing;

/**
 * نتیجهٔ اجرای یک TestSuite کامل.
 */
final class TestSuiteResult
{
    /**
     * @param int $total
     * @param int $passed
     * @param int $failed
     * @param list<TestResult> $results
     */
    public function __construct(
        public readonly int $total,
        public readonly int $passed,
        public readonly int $failed,
        public readonly array $results,
    ) {
    }

    public function isPassed(): bool
    {
        return $this->failed === 0 && $this->total > 0;
    }

    public function summary(): string
    {
        $status = $this->isPassed() ? 'PASS' : 'FAIL';
        return \sprintf("%s: %d/%d (failed: %d)", $status, $this->passed, $this->total, $this->failed);
    }
}
