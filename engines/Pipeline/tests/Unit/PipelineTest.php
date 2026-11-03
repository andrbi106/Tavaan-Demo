<?php

declare(strict_types=1);

namespace Tavaan\Engine\Pipeline\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Pipeline\Pipeline;
use Tavaan\Engine\Pipeline\Contracts\MiddlewareInterface;

/**
 * تست‌های Unit برای موتور Pipeline.
 *
 * @see docs/03-engines/pipeline.md (DOC-034)
 */
final class PipelineTest extends TestCase
{
    public function test_basic_pipeline_with_closures(): void
    {
        $result = (new Pipeline())
            ->send('input')
            ->through([
                fn($p, $next) => $next($p . '+1'),
                fn($p, $next) => $next($p . '+2'),
            ])
            ->then(fn($p) => $p . '|final');

        $this->assertSame('input+1+2|final', $result);
    }

    public function test_pipeline_order_is_onion(): void
    {
        $log = [];
        $result = (new Pipeline())
            ->send('payload')
            ->through([
                function ($p, $next) use (&$log) {
                    $log[] = 'A_in';
                    $r = $next($p);
                    $log[] = 'A_out';
                    return $r;
                },
                function ($p, $next) use (&$log) {
                    $log[] = 'B_in';
                    $r = $next($p);
                    $log[] = 'B_out';
                    return $r;
                },
            ])
            ->then(function ($p) use (&$log) {
                $log[] = 'destination';
                return $p;
            });

        // Onion order: A_in → B_in → destination → B_out → A_out
        $this->assertSame(['A_in', 'B_in', 'destination', 'B_out', 'A_out'], $log);
    }

    public function test_pipe_method_adds_one(): void
    {
        $pipeline = (new Pipeline())
            ->send('input')
            ->pipe(fn($p, $next) => $next($p . '+1'))
            ->pipe(fn($p, $next) => $next($p . '+2'))
            ->then(fn($p) => $p);

        $this->assertSame('input+1+2', $pipeline);
    }

    public function test_pipes_returns_list(): void
    {
        $p = new Pipeline();
        $p->pipe(fn() => null);
        $this->assertCount(1, $p->pipes());
    }

    public function test_through_with_iterable(): void
    {
        $iter = new \ArrayIterator([
            fn($p, $next) => $next($p . '1'),
            fn($p, $next) => $next($p . '2'),
        ]);
        $result = (new Pipeline())
            ->send('x')
            ->through($iter)
            ->then(fn($p) => $p);
        $this->assertSame('x12', $result);
    }

    public function test_via_default_handle(): void
    {
        $middleware = new class {
            public function handle($passable, $next) {
                return $next($passable . '_handled');
            }
        };
        $result = (new Pipeline())
            ->send('input')
            ->through([$middleware])
            ->then(fn($p) => $p);
        $this->assertSame('input_handled', $result);
    }

    public function test_via_custom_method(): void
    {
        $middleware = new class {
            public function process($passable, $next) {
                return $next($passable . '_processed');
            }
        };
        $result = (new Pipeline())
            ->send('input')
            ->through([$middleware])
            ->via('process')
            ->then(fn($p) => $p);
        $this->assertSame('input_processed', $result);
    }

    public function test_string_pipe_with_params(): void
    {
        $middleware = new class {
            public function handle($passable, $next, ...$params) {
                return $next($passable . ':' . \implode(',', $params));
            }
        };
        $container = new class($middleware) {
            private $m;
            public function __construct($m) { $this->m = $m; }
            public function make(string $abs) {
                return $this->m;
            }
        };
        $result = (new Pipeline($container))
            ->send('input')
            ->through(['MyMiddleware:web,admin'])
            ->then(fn($p) => $p);
        $this->assertSame('input:web,admin', $result);
    }

    public function test_string_pipe_fallback_to_classname(): void
    {
        // Pipeline should handle the case where container is null but class exists
        $result = (new Pipeline())
            ->send('input')
            ->through([SimplePipe::class])
            ->then(fn($p) => $p);
        $this->assertSame('input_simple', $result);
    }

    public function test_string_pipe_uses_pipe_classname_with_params(): void
    {
        $result = (new Pipeline())
            ->send('input')
            ->through([ParamPipe::class . ':param1,param2'])
            ->then(fn($p) => $p);
        $this->assertSame('input:p1=param1,p2=param2', $result);
    }

    public function test_thenReturn_uses_identity_destination(): void
    {
        $result = (new Pipeline())
            ->send('input')
            ->through([fn($p, $next) => $next(\strtoupper($p))])
            ->thenReturn();
        $this->assertSame('INPUT', $result);
    }

    public function test_after_stages(): void
    {
        $log = [];
        $pipeline = (new Pipeline())
            ->after(function ($p, $r) use (&$log) { $log[] = "after1:{$p}|{$r}"; })
            ->after(function ($p, $r) use (&$log) { $log[] = "after2:{$p}|{$r}"; });

        $result = $pipeline
            ->send('input')
            ->through([fn($p, $next) => $next($p . '+p')])
            ->then(fn($p) => $p . '|done');

        $pipeline->terminate('input', $result);
        $this->assertSame(['after1:input|input+p|done', 'after2:input|input+p|done'], $log);
    }

    public function test_empty_pipeline(): void
    {
        $result = (new Pipeline())
            ->send('input')
            ->through([])
            ->then(fn($p) => $p . '|final');
        $this->assertSame('input|final', $result);
    }

    public function test_pipeline_passes_passable_to_destination(): void
    {
        $received = null;
        $result = (new Pipeline())
            ->send('the_passable')
            ->through([fn($p, $next) => $next($p . '!')])
            ->then(function ($p) use (&$received) {
                $received = $p;
                return 'done';
            });
        $this->assertSame('the_passable!', $received);
        $this->assertSame('done', $result);
    }

    public function test_middleware_interface_implementation(): void
    {
        $middleware = new class implements MiddlewareInterface {
            public bool $terminated = false;
            public mixed $terminatedPassable = null;
            public mixed $terminatedResult = null;

            public function handle(mixed $passable, \Closure $next, string ...$params): mixed {
                $passable .= '_handle';
                return $next($passable);
            }
            public function terminate(mixed $passable, mixed $result): void {
                $this->terminated = true;
                $this->terminatedPassable = $passable;
                $this->terminatedResult = $result;
            }
        };

        $result = (new Pipeline())
            ->send('input')
            ->through([$middleware])
            ->then(fn($p) => $p . '_final');

        $this->assertSame('input_handle_final', $result);
        $this->assertFalse($middleware->terminated);

        // Call terminate manually
        $middleware->terminate('input', $result);
        $this->assertTrue($middleware->terminated);
        $this->assertSame('input', $middleware->terminatedPassable);
        $this->assertSame('input_handle_final', $middleware->terminatedResult);
    }

    public function test_pipe_terminates_early(): void
    {
        $log = [];
        $result = (new Pipeline())
            ->send('input')
            ->through([
                function ($p, $next) use (&$log) {
                    $log[] = 'first_in';
                    $r = $next($p);
                    $log[] = 'first_out';
                    return $r;
                },
                function ($p, $next) use (&$log) {
                    $log[] = 'second_in';
                    $log[] = 'short_circuit';
                    return 'short_circuit';
                },
                function ($p, $next) use (&$log) {
                    $log[] = 'third_in';
                    return $next($p);
                },
            ])
            ->then(fn($p) => $p . '_final');

        $this->assertSame('short_circuit', $result);
        $this->assertNotContains('third_in', $log);
        $this->assertContains('first_out', $log);
    }
}

// Helper classes

final class SimplePipe
{
    public function handle(mixed $passable, \Closure $next, string ...$params): mixed {
        return $next($passable . '_simple');
    }
}

final class ParamPipe
{
    public function handle(mixed $passable, \Closure $next, string ...$params): mixed {
        $suffix = \array_map(fn($i, $v) => "p{$i}={$v}", \array_keys($params), $params);
        return $next($passable . ':' . \implode(',', $suffix));
    }
}
