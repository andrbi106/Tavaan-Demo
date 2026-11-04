<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Container (پیاده‌سازی درگاه ۱: pull-based DI)
 *
 * کانتینر تزریق وابستگی. این کلاس یکی از سه درگاه ارتباط بین موتورهاست.
 *
 * امکانات:
 *   - bind() / singleton() — ثبت binding
 *   - make() — resolve با reflection-based auto-resolution
 *   - call() — فراخوانی callable با DI
 *   - alias() — ثبت alias
 *   - instance() — ثبت نمونهٔ موجود
 *   - contextual bindings
 *
 * الگو: Service Container / IoC Container
 */

namespace Tavaan\Core\Container;

use Tavaan\Core\Contracts\Container\ContainerInterface;
use Tavaan\Core\Exceptions\Core\ContainerException;

final class Container implements ContainerInterface
{
    /** @var array<string, Binding> */
    private array $bindings = [];

    /** @var array<string, mixed> singleton instances */
    private array $instances = [];

    /** @var array<string, string> alias => abstract */
    private array $aliases = [];

    /** @var array<string, array<string, string>> contextual: [concrete][abstract] => implementation */
    private array $contextual = [];

    /** @var list<string> resolution stack (برای تشخیص چرخه) */
    private array $resolving = [];

    public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = new Binding($concrete, $shared ? Lifetime::Singleton : Lifetime::Transient);
        unset($this->instances[$abstract]);
    }

    public function singleton(string $abstract, \Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function instance(string $abstract, mixed $instance): mixed
    {
        $this->instances[$abstract] = $instance;
        $this->bindings[$abstract] = new Binding(null, Lifetime::Singleton, true);
        return $instance;
    }

    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        $abstract = $this->resolveAlias($abstract);

        // اگر instance قبلاً ساخته شده
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            // auto-resolve: اگر class exists باشد
            if (class_exists($abstract)) {
                $this->bindings[$abstract] = new Binding($abstract, Lifetime::Transient);
            } else {
                throw ContainerException::forUnresolved($abstract);
            }
        }

        $binding = $this->bindings[$abstract];

        // تشخیص چرخه
        if (in_array($abstract, $this->resolving, true)) {
            throw new ContainerException(
                'core.container.circular',
                ['abstract' => $abstract, 'stack' => $this->resolving],
                "چرخهٔ وابستگی هنگام resolve کردن '{$abstract}'."
            );
        }

        $this->resolving[] = $abstract;

        try {
            $object = $this->build($binding->concrete, $parameters);
        } finally {
            array_pop($this->resolving);
        }

        if ($binding->lifetime === Lifetime::Singleton) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function call(callable|string $callback, array $parameters = []): mixed
    {
        if (is_string($callback)) {
            // "Class@method" یا "Class::method"
            if (str_contains($callback, '@')) {
                [$class, $method] = explode('@', $callback, 2);
                $instance = $this->make($class);
                $callback = [$instance, $method];
            } elseif (str_contains($callback, '::')) {
                [$class, $method] = explode('::', $callback, 2);
                $callback = [$class, $method];
            } else {
                $callback = $this->make($callback);
            }
        }

        if (is_array($callback)) {
            [$objectOrClass, $method] = $callback;
            if (is_string($objectOrClass)) {
                $object = $this->make($objectOrClass);
            } else {
                $object = $objectOrClass;
            }
            $reflection = new \ReflectionMethod($object, $method);
            $args = $this->resolveParameters($reflection->getParameters(), $parameters);
            return $reflection->invokeArgs($object, $args);
        }

        if ($callback instanceof \Closure) {
            $reflection = new \ReflectionFunction($callback);
            $args = $this->resolveParameters($reflection->getParameters(), $parameters);
            return $reflection->invokeArgs($args);
        }

        if (is_object($callback) && method_exists($callback, '__invoke')) {
            $reflection = new \ReflectionMethod($callback, '__invoke');
            $args = $this->resolveParameters($reflection->getParameters(), $parameters);
            return $reflection->invokeArgs($callback, $args);
        }

        throw new ContainerException(
            'core.container.invalid_callback',
            [],
            'Callable نامعتبر برای Container::call().'
        );
    }

    public function has(string $id): bool
    {
        $id = $this->resolveAlias($id);
        return isset($this->bindings[$id]) || isset($this->instances[$id]) || class_exists($id);
    }

    public function bound(string $abstract): bool
    {
        $abstract = $this->resolveAlias($abstract);
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw ContainerException::forUnresolved($id);
        }
        return $this->make($id);
    }

    /**
     * افزودن contextual binding.
     *
     * @param string $concrete   کلاسی که وابستگی در آن resolve می‌شود
     * @param string $abstract   وابستگی مورد‌نیاز
     * @param string $implementation پیاده‌سازی جایگزین
     */
    public function addContextualBinding(string $concrete, string $abstract, string $implementation): void
    {
        $this->contextual[$concrete][$abstract] = $implementation;
    }

    /**
     * ساخت یک شیء از concrete با auto-resolution.
     */
    private function build(\Closure|string|null $concrete, array $parameters = []): mixed
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }

        if ($concrete === null) {
            throw ContainerException::forNotInstantiable('(null)');
        }

        if (!class_exists($concrete)) {
            throw ContainerException::forNotInstantiable($concrete);
        }

        $reflection = new \ReflectionClass($concrete);

        if (!$reflection->isInstantiable()) {
            throw ContainerException::forNotInstantiable($concrete);
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $concrete();
        }

        $args = $this->resolveParameters($constructor->getParameters(), $parameters, $concrete);
        return $reflection->newInstanceArgs($args);
    }

    /**
     * Resolve پارامترهای یک متد با auto-DI.
     *
     * @param list<\ReflectionParameter> $parameters
     * @param array<string, mixed> $override
     * @param string|null $concrete کلاس contextual (برای contextual bindings)
     * @return list<mixed>
     */
    private function resolveParameters(array $parameters, array $override = [], ?string $concrete = null): array
    {
        $args = [];
        foreach ($parameters as $param) {
            $name = $param->getName();
            $type = $param->getType();

            // اگر پارامتر در override موجود است
            if (array_key_exists($name, $override)) {
                $args[] = $override[$name];
                continue;
            }

            // contextual binding
            if ($concrete !== null && $type instanceof \ReflectionNamedType) {
                $typeName = $type->getName();
                if (isset($this->contextual[$concrete][$typeName])) {
                    $args[] = $this->make($this->contextual[$concrete][$typeName]);
                    continue;
                }
            }

            // auto-resolve با reflection
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin() && class_exists($type->getName())) {
                try {
                    $args[] = $this->make($type->getName());
                    continue;
                } catch (ContainerException) {
                    // اگر resolve نشد، به default برو
                }
            }

            // default value
            if ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
                continue;
            }

            // nullable
            if ($type instanceof \ReflectionNamedType && $type->allowsNull()) {
                $args[] = null;
                continue;
            }

            // variadic
            if ($param->isVariadic()) {
                $args = array_merge($args, array_values($override));
                continue;
            }

            throw new ContainerException(
                'core.container.unresolved_parameter',
                ['parameter' => $name, 'type' => $type?->__toString()],
                "نمی‌توان پارامتر '\${$name}' را resolve کرد."
            );
        }
        return $args;
    }

    private function resolveAlias(string $abstract): string
    {
        while (isset($this->aliases[$abstract])) {
            $abstract = $this->aliases[$abstract];
        }
        return $abstract;
    }

    /**
     * پاک کردن همهٔ bindings و instances.
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
        $this->contextual = [];
        $this->resolving = [];
    }
}
