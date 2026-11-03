<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability;

/**
 * HealthCheck — بررسی سلامت سرویس.
 */
final class HealthCheck
{
    /** @var array<string, callable(): bool> */
    private array $checks = [];

    public function add(string $name, callable $check): self
    {
        $this->checks[$name] = $check;
        return $this;
    }

    /**
     * @return array{name: string, status: bool, healthy: bool, checks: array<string, bool>}
     */
    public function status(): array
    {
        $results = [];
        $allHealthy = true;
        foreach ($this->checks as $name => $check) {
            try {
                $ok = (bool) $check();
                if (!$ok) $allHealthy = false;
                $results[$name] = $ok;
            } catch (\Throwable) {
                $results[$name] = false;
                $allHealthy = false;
            }
        }
        return [
            'name' => 'tavaan',
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'healthy' => $allHealthy,
            'checks' => $results,
        ];
    }

    public function liveness(): bool
    {
        return true;  // ساده — اگر در حال اجرا هستیم، live هستیم
    }

    public function readiness(): bool
    {
        $status = $this->status();
        return $status['healthy'];
    }
}

/**
 * ObservabilityServiceProvider.
 */
final class ObservabilityServiceProvider
{
    private ?MetricsManager $metrics = null;
    private ?TraceManager $trace = null;
    private ?HealthCheck $health = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->metrics = new MetricsManager();
        $this->trace = new TraceManager();
        $this->health = new HealthCheck();
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [MetricsManager::class, TraceManager::class, HealthCheck::class];
    }

    public function getMetrics(): MetricsManager
    {
        if ($this->metrics === null) $this->register();
        return $this->metrics;
    }

    public function getTrace(): TraceManager
    {
        if ($this->trace === null) $this->register();
        return $this->trace;
    }

    public function getHealth(): HealthCheck
    {
        if ($this->health === null) $this->register();
        return $this->health;
    }
}
