# Tavaan Engine — Schema

> **ساختار دیتابیس** — موتور Schema در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.schema` |
| نام | `Schema` |
| عنوان فارسی | ساختار دیتابیس |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Database\Schema` |
| کلاس موتور | `Tavaan\Engine\Database\Schema\SchemaEngine` |
| Facade | Schema (`schema`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Database\Schema;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class SchemaEngine extends BaseEngine
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

**فایل:** `src/SchemaEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `database`

## Service Providerها

- `Tavaan\Engine\Database\Schema\SchemaServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Schema.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
