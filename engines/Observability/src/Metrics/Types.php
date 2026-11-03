<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability\Metrics;

/**
 * Counter — افزایشی.
 */
final class Counter
{
    /** @var array<string, float> */
    private array $values = [];

    public function __construct(
        public readonly string $name,
        public readonly string $help = '',
    ) {
    }

    public function increment(float $value = 1, array $labels = []): void
    {
        $key = $this->labelKey($labels);
        $this->values[$key] = ($this->values[$key] ?? 0) + $value;
    }

    public function get(array $labels = []): float
    {
        return $this->values[$this->labelKey($labels)] ?? 0;
    }

    /** @return array<string, float> */
    public function all(): array
    {
        return $this->values;
    }

    private function labelKey(array $labels): string
    {
        \ksort($labels);
        return \json_encode($labels) ?: '';
    }
}

/**
 * Gauge — افزایشی/کاهشی.
 */
final class Gauge
{
    /** @var array<string, float> */
    private array $values = [];

    public function __construct(public readonly string $name, public readonly string $help = '') {}

    public function set(float $value, array $labels = []): void
    {
        $this->values[$this->labelKey($labels)] = $value;
    }

    public function get(array $labels = []): float
    {
        return $this->values[$this->labelKey($labels)] ?? 0;
    }

    /** @return array<string, float> */
    public function all(): array { return $this->values; }

    private function labelKey(array $labels): string
    {
        \ksort($labels);
        return \json_encode($labels) ?: '';
    }
}

/**
 * Histogram — توزیع.
 */
final class Histogram
{
    /** @var array<string, array{count: int, sum: float, buckets: array<int, int>}> */
    private array $values = [];

    /** @var list<float> */
    private array $buckets = [0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2.5, 5, 10];

    public function __construct(public readonly string $name, public readonly string $help = '') {}

    public function observe(float $value, array $labels = []): void
    {
        $key = $this->labelKey($labels);
        if (!isset($this->values[$key])) {
            $this->values[$key] = ['count' => 0, 'sum' => 0.0, 'buckets' => \array_fill(0, \count($this->buckets), 0)];
        }
        $this->values[$key]['count']++;
        $this->values[$key]['sum'] += $value;
        foreach ($this->buckets as $i => $threshold) {
            if ($value <= $threshold) {
                $this->values[$key]['buckets'][$i]++;
            }
        }
    }

    /** @return array<string, array{count: int, sum: float, buckets: array<int, int>}> */
    public function all(): array { return $this->values; }

    private function labelKey(array $labels): string
    {
        \ksort($labels);
        return \json_encode($labels) ?: '';
    }
}
