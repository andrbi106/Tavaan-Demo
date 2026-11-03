<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container\Contracts;

/**
 * قرارداد Container (DI).
 *
 * @see \Tavaan\Engine\Container\Container
 * @api 1.0
 * @see docs/03-engines/container.md (DOC-031)
 */
interface ContainerInterface
{
    /**
     * ثبت یک binding (Transient به‌طور پیش‌فرض).
     *
     * @param \Closure|class-string|null $concrete
     */
    public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void;

    /** ثبت Singleton (یک نمونه برای کل فرایند). */
    public function singleton(string $abstract, \Closure|string|null $concrete = null): void;

    /** ثبت Scoped (یک نمونه به ازای هر Scope فعال: Request/Job/Tenant). */
    public function scoped(string $abstract, \Closure|string|null $concrete = null): void;

    /** ثبت Factory (هر make = فراخوانی closure). */
    public function factory(string $abstract, \Closure $factory): void;

    /** ثبت یک نمونهٔ از پیش ساخته‌شده. */
    public function instance(string $abstract, mixed $instance): void;

    /** ثبت alias. */
    public function alias(string $abstract, string $alias): void;

    /**
     * ساخت/Resolve یک سرویس.
     *
     * @param array<string, mixed> $parameters override پارامترها
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /** گرفتن یک سرویس (alias برای make). */
    public function get(string $abstract): mixed;

    /** بررسی وجود binding. */
    public function bound(string $abstract): bool;

    /** حذف یک binding. */
    public function forget(string $abstract): void;

    /**_flush همهٔ bindings و instances. */
    public function flush(): void;

    /**
     * Contextual binding.
     *
     * @return \Tavaan\Engine\Container\ContextualBindingBuilder
     */
    public function when(string $concrete): \Tavaan\Engine\Container\ContextualBindingBuilder;

    /** افزودن extender (decorator). */
    public function extend(string $abstract, \Closure $extender): void;

    /** افزودن tag به کلاس‌ها. */
    public function tag(array $concretes, string $tag): void;

    /** Resolve همهٔ کلاس‌های یک tag. */
    public function tagged(string $tag): iterable;

    /**
     * Call a callable with method injection.
     *
     * @param callable|array<string>|string $callback
     * @param array<string, mixed> $parameters
     */
    public function call(callable|array|string $callback, array $parameters = []): mixed;

    /** @param list<string> $tags */
    public function taggedKeys(): array;

    /** Resolve با تلاش برای ساخت اگر binding نباشد. */
    public function build(string $concrete, array $parameters = []): mixed;
}
