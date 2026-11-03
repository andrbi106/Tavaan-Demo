<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability;

/**
 * Metrics — counter / gauge / histogram / timer registry.
 */
final class Metrics
{
    /** @var array<string, float> */
    private array $counters = [];
    /** @var array<string, float> */
    private array $gauges = [];
    /** @var array<string, array<float>> */
    private array $histograms = [];
    /** @var array<string, float> */
    private array $timers = [];

    /**
     * counter — increment a counter by $value (default 1).
     * To read the current value, use countOf().
     * @param array<string, mixed> $labels
     */
    public function counter(string $name, float $value = 1.0, array $labels = []): self
    {
        $key = $name . $this->labelsKey($labels);
        $this->counters[$key] = ($this->counters[$key] ?? 0.0) + $value;
        return $this;
    }

    /**
     * countOf — return the current value of a counter.
     * @param array<string, mixed> $labels
     */
    public function countOf(string $name, array $labels = []): float
    {
        $key = $name . $this->labelsKey($labels);
        return $this->counters[$key] ?? 0.0;
    }

    /**
     * gauge — set or return gauge value.
     * @return float|self
     */
    public function gauge(string $name, ?float $value = null, array $labels = []): float|self
    {
        $key = $name . $this->labelsKey($labels);
        if ($value === null) {
            return $this->gauges[$key] ?? 0.0;
        }
        $this->gauges[$key] = $value;
        return $this;
    }

    /**
     * histogram — observe a value or return observations.
     * @return array<float>|self
     */
    public function histogram(string $name, ?float $value = null, array $labels = []): array|self
    {
        $key = $name . $this->labelsKey($labels);
        if ($value === null) {
            return $this->histograms[$key] ?? [];
        }
        $this->histograms[$key][] = $value;
        return $this;
    }

    /**
     * timer — start/stop timing or return elapsed.
     * @return float|self
     */
    public function timer(string $name, ?float $elapsedMs = null, array $labels = []): float|self
    {
        $key = $name . $this->labelsKey($labels);
        if ($elapsedMs === null) {
            // start: return a starter closure
            $start = microtime(true);
            $this->timers[$key . '_start'] = $start;
            return $this;
        }
        $this->histograms[$key][] = $elapsedMs;
        return $this;
    }

    /**
     * collect — return all metrics in Prometheus-like format.
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        return [
            'counters' => $this->counters,
            'gauges' => $this->gauges,
            'histograms' => $this->histograms,
            'timers' => $this->timers,
        ];
    }

    private function labelsKey(array $labels): string
    {
        if ($labels === []) { return ''; }
        ksort($labels);
        $parts = [];
        foreach ($labels as $k => $v) {
            $parts[] = "{$k}=" . (string) $v;
        }
        return '{' . implode(',', $parts) . '}';
    }
}
