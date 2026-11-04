<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Pipeline Engine (موتور خط لوله)
 *
 * این کلاس از موتور والد (Tavaan\EngineCore\Engine) ارث می‌برد
 * و چرخهٔ عمر موتور Pipeline را مدیریت می‌کند.
 *
 * چرخهٔ عمر:
 *   register() → boot() → (runtime) → shutdown()
 *
 * سه درگاه ارتباط با سایر موتورها:
 *   ۱. Container (make) — pull-based DI: resolve سرویس از کانتینر
 *   ۲. Events (dispatch/listen) — pub/sub: انتشار و شنود رویداد
 *   ۳. EngineBus (call/offer) — point-to-point RPC: فراخوانی مستقیم موتور دیگر
 *
 * @see \Tavaan\EngineCore\Engine
 */

namespace Tavaan\Engine\Pipeline;

use Tavaan\Core\Contracts\Container\ContainerInterface;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;
use Tavaan\EngineCore\Engine as BaseEngine;

final class PipelineEngine extends BaseEngine
{
    public function __construct(EngineManifestInterface $manifest)
    {
        parent::__construct($manifest);
    }

    /**
     * Stage ۱ — ثبت bindingها در Container.
     *
     * parent::register() به‌طور خودکار Service Providerهای تعریف‌شده
     * در manifest را ثبت می‌کند. در صورت نیاز، bindingهای اضافی
     * را در اینجا ثبت کنید.
     */
    public function register(ContainerInterface $container): void
    {
        parent::register($container);

        // bindingهای اختصاصی موتور (در صورت نیاز)
        // مثال:
        // $container->singleton(SomeInterface::class, fn() => new SomeImplementation());
    }

    /**
     * Stage ۲ — راه‌اندازی نهایی موتور.
     *
     * پس از ثبت همهٔ موتورها، این متد فراخوانی می‌شود.
     * parent::boot() flag booted را ست می‌کند.
     */
    public function boot(): void
    {
        // راه‌اندازی نهایی موتور (در صورت نیاز)
        // مثال:
        // $this->listen('user.registered', [$this, 'onUserRegistered']);

        parent::boot();
    }

    /**
     * خاموش‌سازی موتور — cleanup منابع.
     */
    public function shutdown(): void
    {
        // cleanup (در صورت نیاز)
        parent::shutdown();
    }
}
