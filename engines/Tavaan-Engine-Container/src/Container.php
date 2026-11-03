<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container;

use Tavaan\Engine\Container\Contracts\ContainerInterface;
use Tavaan\Engine\Container\ContextualBindingBuilder;
use Tavaan\Engine\Container\Exceptions\BindingResolutionException;
use Tavaan\Engine\Container\Exceptions\NotInstantiableException;
use Tavaan\Engine\Container\Binding;
use Tavaan\Engine\Container\Lifetime;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Container — ظرف تزریق وابستگی کامل.
 *
 * امکانات:
 * - Bind/Singleton/Scoped/Factory/Instance
 * - Alias
 * - Auto-wiring با Reflection
 * - Contextual binding (when→needs→give)
 * - Extender/Decorator
 * - Tagging
 * - Method injection
 * - Cycle detection
 *
 * @see docs/03-engines/container.md (DOC-031)
 */
final class Container implements ContainerInterface
{
    /**
     * @var array<string, Binding>
     */
    private array $bindings = [];

    /**
     * @var array<string, mixed>
     */
    private array $instances = [];

    /**
     * @var array<string, string>
     */
    private array $aliases = [];

    /**
     * @var array<string, list<string>>
     */
    private array $tags = [];

    /**
     * @var array<string, list<\Closure>>
     */
    private array $extenders = [];

    /**
     * @var array<string, array<string, \Closure|string|mixed>>
     */
    private array $contextual = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $scopedInstances = [];

    /**
     * @var list<string>
     */
    private array $buildStack = [];

    /**
     * @var array<class-string, array{ctor: \ReflectionMethod|null, params: list<ReflectionParameter>}>
     */
    private array $reflectionCache = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
        // Register self
        $this->instance(ContainerInterface::class, $this);
        $this->instance(self::class, $this);
        $this->alias(ContainerInterface::class, 'container');
        $this->alias(self::class, 'app');
    }

    public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void
    {
        $concrete ??= $abstract;
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: $concrete,
            lifetime: $shared ? Lifetime::Singleton : Lifetime::Transient,
            shared: $shared,
        );
        // Clear any existing singleton instance
        unset($this->instances[$abstract]);
    }

    public function singleton(string $abstract, \Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: $concrete ?? $abstract,
            lifetime: Lifetime::Singleton,
            shared: true,
        );
    }

    public function scoped(string $abstract, \Closure|string|null $concrete = null): void
    {
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: $concrete ?? $abstract,
            lifetime: Lifetime::Scoped,
            shared: true,
        );
    }

    public function factory(string $abstract, \Closure $factory): void
    {
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: $factory,
            lifetime: Lifetime::Factory,
            shared: false,
        );
    }

    public function instance(string $abstract, mixed $instance): void
    {
        $this->bindings[$abstract] = new Binding(
            abstract: $abstract,
            concrete: $abstract,
            lifetime: Lifetime::Instance,
            shared: true,
        );
        $this->instances[$abstract] = $instance;
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        // Resolve alias
        $abstract = $this->resolveAlias($abstract);

        // If instance already cached, return it
        if (isset($this->instances[$abstract])) {
            return $this->applyExtenders($abstract, $this->instances[$abstract], $parameters);
        }

        // Get binding
        $binding = $this->bindings[$abstract] ?? null;

        // No binding — try to auto-build
        if ($binding === null) {
            if (!\class_exists($abstract)) {
                throw BindingResolutionException::forAbstract($abstract);
            }
            $object = $this->build($abstract, $parameters);
            return $this->applyExtenders($abstract, $object, $parameters);
        }

        // Factory: always fresh
        if ($binding->lifetime === Lifetime::Factory) {
            $concrete = $binding->concrete;
            \assert($concrete instanceof \Closure);
            $object = $concrete($this, $parameters);
            return $this->applyExtenders($abstract, $object, $parameters);
        }

        // Transient: always fresh
        if ($binding->lifetime === Lifetime::Transient) {
            $object = $this->buildFromBinding($binding, $parameters);
            return $this->applyExtenders($abstract, $object, $parameters);
        }

        // Singleton / Scoped / Instance: cache and return
        $object = $this->buildFromBinding($binding, $parameters);
        if ($binding->lifetime === Lifetime::Singleton || $binding->lifetime === Lifetime::Instance) {
            $this->instances[$abstract] = $object;
        } elseif ($binding->lifetime === Lifetime::Scoped) {
            $this->scopedInstances[$abstract] = $object;
        }
        return $this->applyExtenders($abstract, $object, $parameters);
    }

    public function get(string $abstract): mixed
    {
        return $this->make($abstract);
    }

    public function bound(string $abstract): bool
    {
        $abstract = $this->resolveAlias($abstract);
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    public function forget(string $abstract): void
    {
        $abstract = $this->resolveAlias($abstract);
        unset($this->bindings[$abstract], $this->instances[$abstract]);
    }

    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
        $this->tags = [];
        $this->extenders = [];
        $this->contextual = [];
        $this->scopedInstances = [];
        $this->buildStack = [];
    }

    public function when(string $concrete): ContextualBindingBuilder
    {
        return new ContextualBindingBuilder($this, $concrete);
    }

    public function extend(string $abstract, \Closure $extender): void
    {
        $this->extenders[$abstract][] = $extender;
    }

    public function tag(array $concretes, string $tag): void
    {
        foreach ($concretes as $abstract) {
            $this->tags[$tag][] = $abstract;
        }
    }

    public function tagged(string $tag): iterable
    {
        foreach ($this->tags[$tag] ?? [] as $abstract) {
            yield $this->make($abstract);
        }
    }

    public function taggedKeys(): array
    {
        return \array_keys($this->tags);
    }

    public function call(callable|array|string $callback, array $parameters = []): mixed
    {
        if (\is_string($callback)) {
            // Class@method syntax
            if (\str_contains($callback, '@')) {
                [$class, $method] = \explode('@', $callback, 2);
                $instance = $this->make($class);
                return $this->invokeMethod([$instance, $method], $parameters);
            }
            // Just a function
            return $this->invokeFunction($callback, $parameters);
        }
        if (\is_array($callback)) {
            return $this->invokeMethod($callback, $parameters);
        }
        if ($callback instanceof \Closure) {
            return $this->invokeFunction($callback, $parameters);
        }
        // Callable object
        return ($callback)($this, $parameters);
    }

    public function build(string $concrete, array $parameters = []): mixed
    {
        if (\in_array($concrete, $this->buildStack, true)) {
            throw BindingResolutionException::cyclic($concrete);
        }
        $this->buildStack[] = $concrete;
        try {
            $signature = $this->reflectionOf($concrete);
            if ($signature['ctor'] === null) {
                return new $concrete();
            }
            $args = [];
            foreach ($signature['params'] as $param) {
                $name = $param->getName();
                if (\array_key_exists($name, $parameters)) {
                    $args[$name] = $parameters[$name];
                    continue;
                }
                // Contextual binding
                $consumer = $this->buildStack[\count($this->buildStack) - 2] ?? null;
                if ($consumer !== null && isset($this->contextual[$consumer][$name])) {
                    $args[$name] = $this->resolveContextual($this->contextual[$consumer][$name]);
                    continue;
                }
                // Type-hinted parameter
                $type = $param->getType();
                if ($type !== null && !$type->isBuiltin()) {
                    $typeName = $type->getName();
                    if ($this->bound($typeName) || \class_exists($typeName)) {
                        $args[$name] = $this->make($typeName);
                        continue;
                    }
                }
                // Default value
                if ($param->isDefaultValueAvailable()) {
                    $args[$name] = $param->getDefaultValue();
                    continue;
                }
                // Nullable parameter
                if ($param->allowsNull()) {
                    $args[$name] = null;
                    continue;
                }
                throw BindingResolutionException::forParameter($concrete, $name);
            }
            return new $concrete(...$args);
        } finally {
            \array_pop($this->buildStack);
        }
    }

    /**
     * Internal API for contextual binding registration.
     *
     * @internal
     */
    public function addContextualBinding(string $consumer, string $param, \Closure|string $value): void
    {
        $this->contextual[$consumer][$param] = $value;
    }

    /**
     * Begin a new scope (e.g. 'request', 'job').
     * Returns previous scope instances for restoration.
     */
    public function beginScope(string $name): array
    {
        $previous = $this->scopedInstances;
        $this->scopedInstances = [];
        return ['name' => $name, 'previous' => $previous];
    }

    /** End scope and clear scoped instances. */
    public function endScope(): void
    {
        $this->scopedInstances = [];
    }

    /** Get all registered abstract names. */
    public function bindings(): array
    {
        return \array_keys($this->bindings);
    }

    /**
     * Resolve alias chain to canonical abstract.
     */
    private function resolveAlias(string $abstract): string
    {
        $seen = [];
        while (isset($this->aliases[$abstract])) {
            if (\in_array($abstract, $seen, true)) {
                throw BindingResolutionException::cyclic($abstract);
            }
            $seen[] = $abstract;
            $abstract = $this->aliases[$abstract];
        }
        return $abstract;
    }

    private function buildFromBinding(Binding $binding, array $parameters): mixed
    {
        $concrete = $binding->concrete;
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }
        if (\is_string($concrete)) {
            return $this->build($concrete, $parameters);
        }
        return $concrete;
    }

    private function applyExtenders(string $abstract, mixed $object, array $parameters): mixed
    {
        foreach ($this->extenders[$abstract] ?? [] as $extender) {
            $object = $extender($object, $this);
        }
        return $object;
    }

    /**
     * @return array{ctor: \ReflectionMethod|null, params: list<ReflectionParameter>}
     */
    private function reflectionOf(string $class): array
    {
        if (isset($this->reflectionCache[$class])) {
            return $this->reflectionCache[$class];
        }
        try {
            $rc = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw NotInstantiableException::forClass($class);
        }
        if (!$rc->isInstantiable()) {
            throw NotInstantiableException::forClass($class);
        }
        $ctor = $rc->getConstructor();
        $params = $ctor?->getParameters() ?? [];
        /** @var array{ctor: \ReflectionMethod|null, params: list<ReflectionParameter>} $signature */
        $signature = ['ctor' => $ctor, 'params' => $params];
        $this->reflectionCache[$class] = $signature;
        return $signature;
    }

    private function resolveContextual(\Closure|string $value): mixed
    {
        if ($value instanceof \Closure) {
            return $value($this);
        }
        if (\is_string($value)) {
            return $this->make($value);
        }
        return $value;
    }

    private function invokeMethod(array $callback, array $parameters): mixed
    {
        [$instance, $method] = $callback;
        if (\is_string($instance)) {
            $instance = $this->make($instance);
        }
        $rm = new ReflectionMethod($instance, $method);
        $args = $this->resolveArgs($rm->getParameters(), $parameters);
        return $rm->invokeArgs($instance, $args);
    }

    private function invokeFunction(\Closure|string $callback, array $parameters): mixed
    {
        $rf = new ReflectionFunction($callback);
        $args = $this->resolveArgs($rf->getParameters(), $parameters);
        return $rf->invokeArgs($args);
    }

    /**
     * @param list<ReflectionParameter> $refParams
     * @param array<string, mixed> $override
     * @return list<mixed>
     */
    private function resolveArgs(array $refParams, array $override): array
    {
        $args = [];
        foreach ($refParams as $param) {
            $name = $param->getName();
            if (\array_key_exists($name, $override)) {
                $args[] = $override[$name];
                continue;
            }
            $type = $param->getType();
            if ($type !== null && !$type->isBuiltin()) {
                $typeName = $type->getName();
                if ($this->bound($typeName) || \class_exists($typeName)) {
                    $args[] = $this->make($typeName);
                    continue;
                }
            }
            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
                continue;
            }
            if ($param->allowsNull()) {
                $args[] = null;
                continue;
            }
            $args[] = null;
        }
        return $args;
    }

    /**
     * dependencyGraph
     */
    public function dependencyGraph(): array
    {
        $graph = [];
        foreach ($this->bindings as $abstract => $binding) {
            $concrete = is_string($binding->concrete) ? $binding->concrete : $abstract;
            $deps = $this->getDependencies($concrete);
            $graph[$abstract] = $deps;
        }
        return $graph;
    }
    /**
     * flushScoped
     */
    public function flushScoped(): void
    {
        $this->scopedInstances = [];
    }
    /**
     * forgetInstance
     */
    public function forgetInstance(string $abstract): void
    {
        $abstract = $this->resolveAlias($abstract);
        unset($this->instances[$abstract]);
    }
    /**
     * has
     */
    public function has(string $key): bool
    {
        return $this->bound($key);
    }
    /**
     * lazy
     */
    public function lazy(string $abstract): mixed
    {
        return new class($this, $abstract) {
            private mixed $instance = null;
            public function __construct(private Container $container, private string $abstract) {}
            public function __invoke(): mixed
            {
                if ($this->instance === null) {
                    $this->instance = $this->container->make($this->abstract);
                }
                return $this->instance;
            }
        };
    }
    /**
     * stats
     */
    public function stats(): array
    {
        return [
            "bindings" => count($this->bindings),
            "instances" => count($this->instances),
            "aliases" => count($this->aliases),
            "tags" => count($this->tags),
        ];
    }
}
