# Tavaan Engine — I18n

> **بین‌المللی‌سازی** — موتور I18n در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.i18n` |
| نام | `I18n` |
| عنوان فارسی | بین‌المللی‌سازی |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\I18n` |
| کلاس موتور | `Tavaan\Engine\I18n\I18nEngine` |
| Facade | Lang (`translator`) |

## کلاس موتور

```php
namespace Tavaan\Engine\I18n;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class I18nEngine extends BaseEngine
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

**فایل:** `src/I18nEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

## Service Providerها

- `Tavaan\Engine\I18n\I18nServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-I18n.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
