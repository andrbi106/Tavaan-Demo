<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event\Contracts;

/**
 * قرارداد Event Dispatcher.
 *
 * @api 1.0
 * @see docs/03-engines/event.md (DOC-033)
 */
interface DispatcherInterface
{
    /**
     * ثبت یک listener.
     *
     * @param string|\Closure $event نام رویداد یا کلاس
     * @param \Closure|string|array|null $listener Closure یا class-string یا [Class, method]
     * @param int $priority اولویت (بزرگ‌تر = اول)
     */
    public function listen(string|\Closure $event, \Closure|string|array|null $listener = null, int $priority = 0): void;

    /**
     * ثبت یک subscriber class.
     */
    public function subscribe(string $subscriber): void;

    /**
     * Dispatch یک رویداد.
     *
     * @param object|string $event شیء رویداد یا نام
     * @param array<string, mixed> $payload آرگومانها (در صورت رشته بودن event)
     * @param bool $halt توقف در اولین خروجی non-null
     * @return mixed|null خروجی halt یا null
     */
    public function dispatch(object|string $event, array $payload = [], bool $halt = false): mixed;

    /**
     * Dispatch و توقف در اولین خروجی non-null.
     */
    public function until(object|string $event, array $payload = []): mixed;

    /**
     * بررسی وجود listener برای رویداد.
     */
    public function hasListeners(string $event): bool;

    /**
     * لیست listenerهای یک رویداد.
     *
     * @return list<callable>
     */
    public function getListeners(string $event): array;

    /**
     * فراموش‌کردن همه‌ی listenerهای یک رویداد.
     */
    public function forget(string $event): void;

    /**
     * Fake mode (برای تست).
     */
    public function fake(array|string $events = []): void;

    /**
     * @param callable|null $callback
     */
    public function assertDispatched(string $event, ?callable $callback = null): void;

    public function assertNotDispatched(string $event): void;

    public function assertDispatchedTimes(string $event, int $times = 1): void;
}
