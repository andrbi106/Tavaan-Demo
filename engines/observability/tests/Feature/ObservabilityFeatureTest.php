<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Observability\ObservabilityServiceProvider;
use Tavaan\Engine\Observability\MetricsManager;
use Tavaan\Engine\Observability\TraceManager;
use Tavaan\Engine\Observability\HealthCheck;

final class ObservabilityFeatureTest extends TestCase
{
    public function test_service_provider_creates_all_managers(): void
    {
        $provider = new ObservabilityServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(MetricsManager::class, $provider->getMetrics());
        $this->assertInstanceOf(TraceManager::class, $provider->getTrace());
        $this->assertInstanceOf(HealthCheck::class, $provider->getHealth());
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/observability.php';
        $this->assertArrayHasKey('error_metric', $m);
    }

    public function test_end_to_end_metrics_and_tracing(): void
    {
        $provider = new ObservabilityServiceProvider([]);
        $provider->register();
        $metrics = $provider->getMetrics();
        $trace = $provider->getTrace();

        $counter = $metrics->counter('requests', 'Total requests');
        $counter->increment(labels: ['route' => '/users']);
        $span = $trace->startSpan('http.request');
        $span->setAttribute('route', '/users');
        $span->end();

        $output = $metrics->collect();
        $this->assertStringContainsString('requests', $output);
        $this->assertCount(1, $trace->spans());
    }
}
