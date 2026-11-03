<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Observability\MetricsManager;
use Tavaan\Engine\Observability\Metrics\Counter;
use Tavaan\Engine\Observability\Metrics\Gauge;
use Tavaan\Engine\Observability\Metrics\Histogram;
use Tavaan\Engine\Observability\TraceManager;
use Tavaan\Engine\Observability\Span;
use Tavaan\Engine\Observability\HealthCheck;
use Tavaan\Engine\Observability\ObservabilityServiceProvider;

final class ObservabilityTest extends TestCase
{
    public function test_counter_increment(): void
    {
        $c = new Counter('requests', 'Total requests');
        $c->increment();
        $c->increment();
        $this->assertSame(2.0, $c->get());
    }

    public function test_counter_with_labels(): void
    {
        $c = new Counter('requests');
        $c->increment(labels: ['route' => '/users']);
        $c->increment(labels: ['route' => '/users']);
        $c->increment(labels: ['route' => '/posts']);
        $this->assertSame(2.0, $c->get(labels: ['route' => '/users']));
        $this->assertSame(1.0, $c->get(labels: ['route' => '/posts']));
    }

    public function test_gauge_set_get(): void
    {
        $g = new Gauge('queue_size');
        $g->set(42.0);
        $this->assertSame(42.0, $g->get());
        $g->set(10.0);
        $this->assertSame(10.0, $g->get());
    }

    public function test_histogram_observe(): void
    {
        $h = new Histogram('duration');
        $h->observe(0.05);
        $h->observe(0.15);
        $h->observe(0.3);
        $all = $h->all();
        $key = \array_keys($all)[0];
        $this->assertSame(3, $all[$key]['count']);
        $this->assertSame(0.5, $all[$key]['sum']);
    }

    public function test_metrics_manager_counter(): void
    {
        $m = new MetricsManager();
        $c = $m->counter('http_requests');
        $c->increment();
        $this->assertSame(1.0, $c->get());
        $same = $m->counter('http_requests');
        $this->assertSame($c, $same);
    }

    public function test_metrics_manager_gauge(): void
    {
        $m = new MetricsManager();
        $g = $m->gauge('memory');
        $g->set(1024.0);
        $this->assertSame(1024.0, $g->get());
    }

    public function test_metrics_manager_histogram(): void
    {
        $m = new MetricsManager();
        $h = $m->histogram('latency');
        $h->observe(0.01);
        $h->observe(0.1);
        $this->assertCount(1, $h->all());
    }

    public function test_metrics_timer(): void
    {
        $m = new MetricsManager();
        $result = $m->timer('test', fn() => 'done');
        $this->assertSame('done', $result);
        $h = $m->histogram('test');
        $all = $h->all();
        $this->assertCount(1, $all);
    }

    public function test_metrics_collect_prometheus_format(): void
    {
        $m = new MetricsManager();
        $m->counter('requests', 'Total requests')->increment();
        $output = $m->collect();
        $this->assertStringContainsString('# HELP requests Total requests', $output);
        $this->assertStringContainsString('# TYPE requests counter', $output);
        $this->assertStringContainsString('requests', $output);
    }

    public function test_metrics_collect_gauge_format(): void
    {
        $m = new MetricsManager();
        $m->gauge('memory', 'Memory usage')->set(1024.0);
        $output = $m->collect();
        $this->assertStringContainsString('# TYPE memory gauge', $output);
        $this->assertStringContainsString('memory', $output);
    }

    public function test_span_basic(): void
    {
        $span = new Span('test.operation');
        $this->assertNotEmpty($span->spanId);
        $this->assertNotEmpty($span->traceId);
        $this->assertSame('ok', $span->status);
        $span->end();
        $this->assertNotNull($span->endTime);
        $this->assertGreaterThanOrEqual(0.0, $span->durationMs());
    }

    public function test_span_attributes(): void
    {
        $span = new Span('test');
        $span->setAttribute('user_id', 42);
        $span->setAttribute('operation', 'query');
        $this->assertSame(42, $span->attributes['user_id']);
        $this->assertSame('query', $span->attributes['operation']);
    }

    public function test_span_status(): void
    {
        $span = new Span('test');
        $span->setStatus('error');
        $this->assertSame('error', $span->status);
    }

    public function test_trace_manager_start_span(): void
    {
        $m = new TraceManager();
        $span = $m->startSpan('op', ['attr' => 1]);
        $this->assertCount(1, $m->spans());
        $this->assertSame('op', $span->attributes['name']);
        $this->assertSame(1, $span->attributes['attr']);
    }

    public function test_trace_manager_clear(): void
    {
        $m = new TraceManager();
        $m->startSpan('a');
        $this->assertCount(1, $m->spans());
        $m->clear();
        $this->assertSame([], $m->spans());
    }

    public function test_traceparent_format(): void
    {
        $m = new TraceManager();
        $span = $m->startSpan('test');
        $tp = $m->traceparent($span);
        $this->assertStringStartsWith('00-', $tp);
        // 32 hex trace_id - 16 hex span_id - 01
        $parts = \explode('-', $tp);
        $this->assertCount(4, $parts);
        $this->assertSame(32, \strlen($parts[1]));
        $this->assertSame(16, \strlen($parts[2]));
    }

    public function test_health_check_healthy(): void
    {
        $h = new HealthCheck();
        $h->add('db', fn() => true);
        $h->add('cache', fn() => true);
        $status = $h->status();
        $this->assertSame('healthy', $status['status']);
        $this->assertTrue($status['healthy']);
        $this->assertTrue($status['checks']['db']);
        $this->assertTrue($status['checks']['cache']);
    }

    public function test_health_check_unhealthy(): void
    {
        $h = new HealthCheck();
        $h->add('db', fn() => true);
        $h->add('cache', fn() => false);
        $status = $h->status();
        $this->assertSame('unhealthy', $status['status']);
        $this->assertFalse($status['healthy']);
    }

    public function test_health_check_with_exception(): void
    {
        $h = new HealthCheck();
        $h->add('failing', fn() => throw new \RuntimeException('fail'));
        $status = $h->status();
        $this->assertFalse($status['checks']['failing']);
    }

    public function test_health_liveness(): void
    {
        $h = new HealthCheck();
        $this->assertTrue($h->liveness());
    }

    public function test_health_readiness(): void
    {
        $h = new HealthCheck();
        $h->add('db', fn() => true);
        $this->assertTrue($h->readiness());
        $h->add('cache', fn() => false);
        $this->assertFalse($h->readiness());
    }

    public function test_service_provider_creates(): void
    {
        $provider = new ObservabilityServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(MetricsManager::class, $provider->getMetrics());
        $this->assertInstanceOf(TraceManager::class, $provider->getTrace());
        $this->assertInstanceOf(HealthCheck::class, $provider->getHealth());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new ObservabilityServiceProvider([]);
        $this->assertContains(MetricsManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/observability.php';
        $this->assertArrayHasKey('metrics', $config);
        $this->assertArrayHasKey('tracing', $config);
        $this->assertArrayHasKey('health', $config);
    }
}
