# Tavaan Engine — Tenancy

> **چنداجاره‌ای** — موتور Tenancy در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.tenancy` |
| نام | `Tenancy` |
| عنوان فارسی | چنداجاره‌ای |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Tenancy` |
| کلاس موتور | `Tavaan\Engine\Tenancy\TenancyEngine` |
| Facade | Tenant (`tenant`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Tenancy;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class TenancyEngine extends BaseEngine
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

**فایل:** `src/TenancyEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

## Service Providerها

- `Tavaan\Engine\Tenancy\TenantServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Tenancy.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
