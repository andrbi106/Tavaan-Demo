# Tavaan Engine — Migration

> **مهاجرت دیتابیس** — موتور Migration در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.migration` |
| نام | `Migration` |
| عنوان فارسی | مهاجرت دیتابیس |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Database\Migration` |
| کلاس موتور | `Tavaan\Engine\Database\Migration\MigrationEngine` |

## کلاس موتور

```php
namespace Tavaan\Engine\Database\Migration;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class MigrationEngine extends BaseEngine
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

**فایل:** `src/MigrationEngine.php`

## توضیحات

Migrator در engines/database/src/Migration (namespace مطابق DOC-049)؛ دستورات CLI در src/Console.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `database`, `schema`, `console`

## Service Providerها

- `Tavaan\Engine\Database\Migration\MigratorServiceProvider`

## جداول دیتابیس

- `tvn_migrations`

## دستورات CLI

- `migrate`
- `migrate:rollback`
- `migrate:status`
- `migrate:fresh`
- `migrate:refresh`
- `migrate:install`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Migration.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
