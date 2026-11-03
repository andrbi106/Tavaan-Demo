# Observability

> رصدپذیری سه‌گانه: Metrics (Prometheus-compatible)، Distributed Tracing، Health Checks.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.observability` |
| DOC-ID | `DOC-067` |
| Namespace | `Tavaan\Engine\Observability` |
| اولویت | P1 (فاز M6) |
| Facade | `Metrics`, `Trace` |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-Observability |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/observability.md

## استفاده

```php
use Tavaan\Engine\Observability\ObservabilityServiceProvider;
use Tavaan\Engine\Observability\MetricsManager;
use Tavaan\Engine\Observability\TraceManager;
use Tavaan\Engine\Observability\HealthCheck;

$provider = new ObservabilityServiceProvider([]);
$provider->register();

// Metrics
$metrics = $provider->getMetrics();
$metrics->counter('http_requests_total', 'Total HTTP requests')->increment(labels: ['status' => '200']);
$metrics->gauge('queue_size')->set(42);
$metrics->histogram('request_duration')->observe(0.034);

// Timer
$result = $metrics->timer('db_query', fn() => $db->query('SELECT ...'));

// Prometheus format output
$prometheus = $metrics->collect();

// Tracing
$trace = $provider->getTrace();
$span = $trace->startSpan('db.query', ['db.system' => 'mysql']);
try {
    // ... do work
    $span->setStatus('ok');
} catch (\Throwable $e) {
    $span->setStatus('error');
    throw $e;
} finally {
    $span->end();
}

// Health Checks
$health = $provider->getHealth();
$health->add('db', fn() => $db->ping());
$health->add('cache', fn() => $cache->ping());
$status = $health->status();
// ['name' => 'tavaan', 'status' => 'healthy', 'healthy' => true, 'checks' => ['db' => true, 'cache' => true]]

$health->liveness();   // true (process alive)
$health->readiness();   // true (all checks pass)
```

## مستندات

[docs/03-engines/observability.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/observability.md)

---
*Generated on 2026-11-03.*
