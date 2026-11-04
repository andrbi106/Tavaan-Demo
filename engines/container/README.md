# Tavaan Engine — Container

> **ظرف سرویس (DI)** — موتور Container در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.container` |
| نام | `Container` |
| عنوان فارسی | ظرف سرویس (DI) |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Container` |
| کلاس موتور | `Tavaan\Engine\Container\ContainerEngine` |
| Facade | App (`app`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Container;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ContainerEngine extends BaseEngine
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

**فایل:** `src/ContainerEngine.php`

## Service Providerها

- `Tavaan\Engine\Container\ContainerServiceProvider`

## دستورات CLI

- `container:graph`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Container.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
