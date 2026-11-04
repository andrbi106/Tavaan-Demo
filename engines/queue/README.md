# Tavaan Engine — Queue

> **صف و Worker** — موتور Queue در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.queue` |
| نام | `Queue` |
| عنوان فارسی | صف و Worker |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Queue` |
| کلاس موتور | `Tavaan\Engine\Queue\QueueEngine` |
| Facade | Queue (`queue`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Queue;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class QueueEngine extends BaseEngine
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

**فایل:** `src/QueueEngine.php`

## توضیحات

M5 — ۵ درایور (sync/database/redis/file/null). Worker مقاوم با Retry/Backoff نمایی، Batch/Chain، Rate Limiting، Failed Jobs + DLQ. 🔒 سریال‌سازی فقط JSON + Allowlist؛ Pop اتمیک با SKIP LOCKED. ♻️ محافظ بازگشت (0.5.1): تشخیص ارجاع دوری + سقف عمق ۶۴ + رد زنجیرهٔ دوری با QueueException تمیز.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `event`

**موتورهای اختیاری:** `database`, `cache`, `log`

## Service Providerها

- `Tavaan\Engine\Queue\QueueManagerServiceProvider`

## جداول دیتابیس

- `tvn_jobs`
- `tvn_failed_jobs`
- `tvn_job_batches`

## دستورات CLI

- `queue:work`
- `queue:listen`
- `queue:restart`
- `queue:retry`
- `queue:failed`
- `queue:flush`
- `queue:prune-failed`
- `queue:monitor`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Queue.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
