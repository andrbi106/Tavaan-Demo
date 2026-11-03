# Tavaan-Engine-Core

> هستهٔ فریمورک Tavaan — قراردادها، خطاها، و کلاس‌های پایه‌ٔ مشترک بین همهٔ موتورها.

این مخزن شامل کلاس‌های فضای‌نام `Tavaan\Core\*` است که موتورهای دیگر به آن‌ها وابسته‌اند:

- `Tavaan\Core\Contracts\*` — قراردادها (interfaceها) برای Cache، Config، Container، Database، Auth، و غیره
- `Tavaan\Core\Exceptions\*` — خطاهای پایه و HTTP خطاهای استاندارد
- `Tavaan\Core\Application\*` — کلاس‌های Application و Builder
- `Tavaan\Core\Container\*` — کانتینر مرکزی
- `Tavaan\Core\Facade\*` — Facadeهای دینامیک
- `Tavaan\Core\Autoloading\*` — Autoloader
- `Tavaan\Core\Bootstrap\*` — Bootstrapperهای application
- `Tavaan\Core\Context\*` — Context classes (Identity, Trace)
- `Tavaan\Core\Events\*` — Event dispatcher مرکزی
- `Tavaan\Core\Error\*` — Error handler

## استفاده

موتورهای دیگر از این کلاس‌ها از طریق `use Tavaan\Core\...` استفاده می‌کنند. این مخزن باید قبل از همهٔ موتورهای دیگر در پروژه autoload شود.

## منبع

این کلاس‌ها از [Derogaramin/Tavaan-Framework/core/](https://github.com/Derogaramin/Tavaan-Framework/tree/main/core) کپی شده‌اند.
