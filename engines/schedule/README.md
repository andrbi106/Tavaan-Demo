# Tavaan Engine — Schedule

> **زمان‌بندی** — موتور Schedule در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.schedule` |
| نام | `Schedule` |
| عنوان فارسی | زمان‌بندی |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Schedule` |
| کلاس موتور | `Tavaan\Engine\Schedule\ScheduleEngine` |
| Facade | Schedule (`schedule`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Schedule;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ScheduleEngine extends BaseEngine
{
    public function __construct(EngineManifestInterface $manifest)
    {
        parent::__construct($manifest);
    }

    public function register(ContainerInterface $container): void
    {
        parent::register($container);
        // bindingهای اختصاصی موتور
    }

    public function boot(): void
    {
        parent::boot();
    }
}
```

**فایل:** `src/ScheduleEngine.php`

## توضیحات

M5 — Cron Expression Parser خودنوشته (۵ فیلد + OR کلاسیک DOM/DOW)، Fluent API، Overlap Prevention با mutex اتمیک، onOneServer، runInBackground. فقط یک entry در crontab: schedule:run.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `console`

**موتورهای اختیاری:** `cache`, `queue`, `log`

## Service Providerها

- `Tavaan\Engine\Schedule\ScheduleManagerServiceProvider`

## دستورات CLI

- `schedule:run`
- `schedule:list`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Schedule.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
