<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability;

use Tavaan\Engine\Observability\Metrics\Counter;
use Tavaan\Engine\Observability\Metrics\Gauge;
use Tavaan\Engine\Observability\Metrics\Histogram;

/**
 * MetricsManager — ثبت و جمع‌آوری metrics.
 */
final class MetricsManager
{
    /** @var array<string, Counter|Gauge|Histogram> */
    private array $metrics = [];

    public function counter(string $name, string $help = ''): Counter
    {
        if (!isset($this->metrics[$name])) {
            $this->metrics[$name] = new Counter($name, $help);
        }
        return $this->metrics[$name];
    }

    public function gauge(string $name, string $help = ''): Gauge
    {
        if (!isset($this->metrics[$name])) {
            $this->metrics[$name] = new Gauge($name, $help);
        }
        return $this->metrics[$name];
    }

    public function histogram(string $name, string $help = ''): Histogram
    {
        if (!isset($this->metrics[$name])) {
            $this->metrics[$name] = new Histogram($name, $help);
        }
        return $this->metrics[$name];
    }

    /**
     * @param callable $callback
     */
    public function timer(string $name, callable $callback, array $labels = []): mixed
    {
        $start = \microtime(true);
        try {
            return $callback();
        } finally {
            $elapsed = \microtime(true) - $start;
            $this->histogram($name)->observe($elapsed, $labels);
        }
    }

    /**
     * @return array<string, Counter|Gauge|Histogram>
     */
    public function all(): array
    {
        return $this->metrics;
    }

    /**
     * خروجی Prometheus text format.
     */
    public function collect(): string
    {
        $output = '';
        foreach ($this->metrics as $name => $metric) {
            if ($metric instanceof Counter) {
                $output .= "# HELP {$name} {$metric->help}
";
                $output .= "# TYPE {$name} counter
";
                foreach ($metric->all() as $labelKey => $value) {
                    $labels = \json_decode($labelKey, true) ?: [];
                    $labelStr = $this->formatLabels($labels);
                    $output .= "{$name}{$labelStr} {$value}
";
                }
            } elseif ($metric instanceof Gauge) {
                $output .= "# HELP {$name} {$metric->help}
";
                $output .= "# TYPE {$name} gauge
";
                foreach ($metric->all() as $labelKey => $value) {
                    $labels = \json_decode($labelKey, true) ?: [];
                    $labelStr = $this->formatLabels($labels);
                    $output .= "{$name}{$labelStr} {$value}
";
                }
            } elseif ($metric instanceof Histogram) {
                $output .= "# HELP {$name} {$metric->help}
";
                $output .= "# TYPE {$name} histogram
";
                foreach ($metric->all() as $labelKey => $data) {
                    $labels = \json_decode($labelKey, true) ?: [];
                    $labelStr = $this->formatLabels($labels);
                    foreach ($data['buckets'] as $i => $count) {
                        $bucketLabels = $this->formatLabels(\array_merge($labels, ['le' => (string) $i]));
                        $output .= "{$name}_bucket{$bucketLabels} {$count}
";
                    }
                    $output .= "{$name}_count{$labelStr} {$data['count']}
";
                    $output .= "{$name}_sum{$labelStr} {$data['sum']}
";
                }
            }
        }
        return $output;
    }

    private function formatLabels(array $labels): string
    {
        if (empty($labels)) return '';
        $parts = [];
        foreach ($labels as $k => $v) {
            $parts[] = "{$k}=\"{$v}\"";
        }
        return '{' . \implode(',', $parts) . '}';
    }
}
