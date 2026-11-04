# Tavaan Engine — View

> **قالب‌ساز** — موتور View در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.view` |
| نام | `View` |
| عنوان فارسی | قالب‌ساز |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\View` |
| کلاس موتور | `Tavaan\Engine\View\ViewEngine` |
| Facade | View (`view`) |

## کلاس موتور

```php
namespace Tavaan\Engine\View;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ViewEngine extends BaseEngine
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

**فایل:** `src/ViewEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `event`, `i18n`, `config`

## Service Providerها

- `Tavaan\Engine\View\ViewServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-View.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
