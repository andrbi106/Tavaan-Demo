# Tavaan Engine — Audit

> **ممیزی** — موتور Audit در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.audit` |
| نام | `Audit` |
| عنوان فارسی | ممیزی |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Audit` |
| کلاس موتور | `Tavaan\Engine\Audit\AuditEngine` |
| Facade | Audit (`audit`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Audit;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class AuditEngine extends BaseEngine
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

**فایل:** `src/AuditEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `event`, `log`, `crypt`, `config`

## Service Providerها

- `Tavaan\Engine\Audit\AuditServiceProvider`

## جداول دیتابیس

- `tvn_audit_logs`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Audit.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
