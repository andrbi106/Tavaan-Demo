# Tavaan Engine — ORM

> **نگاشت شیء-رابطه** — موتور ORM در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.orm` |
| نام | `ORM` |
| عنوان فارسی | نگاشت شیء-رابطه |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Database\ORM` |
| کلاس موتور | `Tavaan\Engine\Database\ORM\ORMEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\Database\ORM;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ORMEngine extends BaseEngine
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

**فایل:** `src/ORMEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `database`, `event`

## Service Providerها

- `Tavaan\Engine\Database\ORM\ORMServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-ORM.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
