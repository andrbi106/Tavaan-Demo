# Tavaan Engine — Jalali

> **تقویم جلالی** — موتور Jalali در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.jalali` |
| نام | `Jalali` |
| عنوان فارسی | تقویم جلالی |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\I18n\Jalali` |
| کلاس موتور | `Tavaan\Engine\I18n\Jalali\JalaliEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\I18n\Jalali;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class JalaliEngine extends BaseEngine
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

**فایل:** `src/JalaliEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `i18n`

## Service Providerها

- `Tavaan\Engine\I18n\Jalali\JalaliServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Jalali.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
