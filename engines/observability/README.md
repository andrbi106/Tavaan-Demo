# Tavaan Engine — Observability

> **رصدپذیری** — موتور Observability در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.observability` |
| نام | `Observability` |
| عنوان فارسی | رصدپذیری |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Observability` |
| کلاس موتور | `Tavaan\Engine\Observability\ObservabilityEngine` |
| Facade | Metrics (`metrics`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Observability;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ObservabilityEngine extends BaseEngine
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

**فایل:** `src/ObservabilityEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `log`

## Service Providerها

- `Tavaan\Engine\Observability\ObservabilityServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Observability.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
