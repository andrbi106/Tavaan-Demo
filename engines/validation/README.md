# Tavaan Engine — Validation

> **اعتبارسنجی** — موتور Validation در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.validation` |
| نام | `Validation` |
| عنوان فارسی | اعتبارسنجی |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Validation` |
| کلاس موتور | `Tavaan\Engine\Validation\ValidationEngine` |
| Facade | Validator (`validator`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Validation;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ValidationEngine extends BaseEngine
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

**فایل:** `src/ValidationEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `i18n`, `config`

## Service Providerها

- `Tavaan\Engine\Validation\ValidationServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Validation.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
