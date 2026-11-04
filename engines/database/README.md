# Tavaan Engine — Database

> **دیتابیس** — موتور Database در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.database` |
| نام | `Database` |
| عنوان فارسی | دیتابیس |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Database` |
| کلاس موتور | `Tavaan\Engine\Database\DatabaseEngine` |
| Facade | DB (`db`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Database;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class DatabaseEngine extends BaseEngine
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

**فایل:** `src/DatabaseEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `event`, `log`

## Service Providerها

- `Tavaan\Engine\Database\DatabaseServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Database.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
