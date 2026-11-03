<?php

declare(strict_types=1);

namespace Tavaan\Engine\Pipeline;

/**
 * Pipeline — عبور دادن یک Payload از زنجیره‌ای از پردازشگرها (onion order).
 *
 * @see docs/03-engines/pipeline.md (DOC-034)
 */
final class Pipeline
{
    private mixed $passable = null;

    /**
     * @var list<string|callable|object>
     */
    private array $pipes = [];

    private string $method = 'handle';

    /**
     * @var list<callable>
     */
    private array $afterStages = [];

    /**
     * @param object|null $container Container (with make() method) for resolving pipes
     */
    public function __construct(
        private readonly ?object $container = null,
    ) {
    }

    public function send(mixed $passable): static
    {
        $this->passable = $passable;
        return $this;
    }

    public function through(array $pipes): static
    {
        $this->pipes = \is_array($pipes) ? \array_values($pipes) : \iterator_to_array($pipes, false);
        return $this;
    }

    public function pipe(string|callable|object $pipe): static
    {
        $this->pipes[] = $pipe;
        return $this;
    }

    public function via(string $method = 'handle'): static
    {
        $this->method = $method;
        return $this;
    }

    /**
     * اجرای زنجیره با destination.
     */
    public function then(callable $destination): mixed
    {
        $pipeline = \array_reduce(
            \array_reverse($this->pipes),
            $this->carry(),
            static fn(mixed $passable): mixed => $destination($passable),
        );

        return $pipeline($this->passable);
    }

    /**
     * اجرای زنجیره با destination = identity (همان passable).
     */
    public function thenReturn(): mixed
    {
        return $this->then(static fn(mixed $passable): mixed => $passable);
    }

    /**
     * افزودن یک stage برای اجرای پس از then().
     */
    public function after(callable $stage): static
    {
        $this->afterStages[] = $stage;
        return $this;
    }

    /**
     * اجرای post-stages (terminate).
     */
    public function terminate(mixed $passable, mixed $result): void
    {
        foreach ($this->afterStages as $stage) {
            $stage($passable, $result);
        }
    }

    /**
     * Get all configured pipes.
     *
     * @return list<string|callable|object>
     */
    public function pipes(): array
    {
        return $this->pipes;
    }

    /**
     * Closure that wraps each pipe around the next.
     */
    private function carry(): \Closure
    {
        $container = $this->container;
        $method = $this->method;
        return function (\Closure $next, string|callable|object $pipe) use ($container, $method): \Closure {
            return function (mixed $passable) use ($next, $pipe, $container, $method): mixed {
                // Closure pipe
                if ($pipe instanceof \Closure || \is_callable($pipe)) {
                    return $pipe($passable, $next);
                }
                // Object pipe — call method directly
                if (\is_object($pipe)) {
                    if (!\method_exists($pipe, $method)) {
                        return $next($passable);
                    }
                    return $pipe->{$method}($passable, $next);
                }
                // String pipe — parse "name:param1,param2" syntax
                [$name, $parameters] = self::parsePipeString($pipe);
                if (\is_string($name)) {
                    $instance = null;
                    if ($container !== null && \method_exists($container, 'make')) {
                        try {
                            $instance = $container->make($name);
                        } catch (\Throwable) {
                            $instance = null;
                        }
                    }
                    if ($instance === null && \class_exists($name)) {
                        $instance = new $name();
                    }
                    if ($instance === null) {
                        // Unknown pipe — pass through
                        return $next($passable);
                    }
                    if (!\method_exists($instance, $method)) {
                        return $next($passable);
                    }
                    return $instance->{$method}(
                        $passable,
                        static fn(mixed $p): mixed => $next($p),
                        ...$parameters,
                    );
                }
                return $next($passable);
            };
        };
    }

    /**
     * 'auth:web,admin' → ['auth', ['web', 'admin']]
     *
     * @return array{0: string|object, 1: list<string>}
     */
    private static function parsePipeString(string|object $pipe): array
    {
        if (!\is_string($pipe)) {
            return [$pipe, []];
        }
        [$name, $params] = \array_pad(\explode(':', $pipe, 2), 2, '');
        $parameters = $params === '' ? [] : \explode(',', $params);
        return [$name, \array_map(\trim(...), $parameters)];
    }
}
