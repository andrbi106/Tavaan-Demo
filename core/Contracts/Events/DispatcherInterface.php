<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Event Dispatcher Interface (درگاه ۲: pub/sub)
 *
 * قرارداد سیستم رویداد. این یکی از سه درگاه ارتباط بین موتورهاست.
 * موتورها رویداد انتشار می‌دهند (dispatch) و سایر موتورها شنونده ثبت می‌کنند (listen).
 */

namespace Tavaan\Core\Contracts\Events;

interface DispatcherInterface
{
    /**
     * ثبت یک شنونده برای رویداد(های) مشخص.
     *
     * @param string|array<string> $events   نام رویداد یا فهرست رویدادها
     * @param callable|string|array $listener شنونده (closure، class name، یا [object, method])
     * @param int                   $priority اولویت (بالا‌تر = زودتر)
     */
    public function listen(string|array $events, callable|string|array $listener, int $priority = 0): void;

    /**
     * انتشار یک رویداد.
     *
     * @param string|object $event   نام رویداد یا شیء رویداد
     * @param mixed         $payload دادهٔ همراه رویداد
     * @param bool          $halt    آیا بعد از اولین response متوقف شود؟
     * @return mixed|null           خروجی شنونده‌ها (در حالت halt) یا null
     */
    public function dispatch(string|object $event, mixed $payload = [], bool $halt = false): mixed;

    /**
     * آیا برای این رویداد شنونده‌ای ثبت شده؟
     */
    public function hasListeners(string $event): bool;

    /**
     * حذف همهٔ شنونده‌های یک رویداد.
     */
    public function forget(string $event): void;
}
