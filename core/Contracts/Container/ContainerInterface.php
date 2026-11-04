<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Container Interface (درگاه ۱: pull-based DI)
 *
 * قرارداد کانتینر تزریق وابستگی. این یکی از سه درگاه ارتباط بین موتورهاست.
 * موتورها سرویس‌های خود را در کانتینر ثبت می‌کنند (bind) و سایر موتورها resolve می‌کنند (make).
 */

namespace Tavaan\Core\Contracts\Container;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * ثبت یک binding.
     *
     * @param string         $abstract  شناسهٔ سرویس (interface یا class name)
     * @param \Closure|string|null $concrete  پیاده‌سازی (closure، class name، یا null برای auto-resolve)
     * @param bool           $shared    آیا singleton باشد؟
     */
    public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void;

    /**
     * ثبت یک singleton (فقط یک نمونه ساخته می‌شود).
     */
    public function singleton(string $abstract, \Closure|string|null $concrete = null): void;

    /**
     * ثبت یک نمونهٔ موجود.
     */
    public function instance(string $abstract, mixed $instance): mixed;

    /**
     * ثبت یک alias برای یک binding.
     */
    public function alias(string $abstract, string $alias): void;

    /**
     * Resolve یک سرویس از کانتینر.
     *
     * @param string  $abstract   شناسهٔ سرویس
     * @param array<string, mixed> $parameters پارامترهای اضافی
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []): mixed;

    /**
     * فراخوانی یک callable با تزریق وابستگی خودکار.
     *
     * @param callable|string $callback
     * @param array<string, mixed> $parameters
     * @return mixed
     */
    public function call(callable|string $callback, array $parameters = []): mixed;

    /**
     * بررسی اینکه یک binding ثبت شده است.
     */
    public function bound(string $abstract): bool;
}
