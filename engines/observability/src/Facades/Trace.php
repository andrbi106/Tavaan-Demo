<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability\Facades;

use Tavaan\Core\Facade\Facade;

/**
 * Facade ردیابی توزیع‌شده (Trace — DOC-067 §3).
 *
 * @method static \Tavaan\Engine\Observability\Contracts\SpanInterface startSpan(string $name, array $attributes = [])
 * @method static \Tavaan\Engine\Observability\Contracts\SpanInterface|null currentSpan()
 * @method static mixed withSpan(string $name, array $attributes, callable $fn)
 * @method static bool isEnabled()
 * @method static int flush()
 * @method static void addExporter(\Tavaan\Engine\Observability\Contracts\SpanExporterInterface $exporter)
 * @mixin \Tavaan\Engine\Observability\Tracing\Tracer
 * @api 1.0
 * @see docs/03-engines/observability.md
 */
final class Trace extends Facade
{
    protected static function accessor(): string
    {
        return 'tracer';
    }
}
