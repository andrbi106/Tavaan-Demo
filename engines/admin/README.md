# Tavaan Engine — Admin

> **پیشخان و CRUD Generator** — موتور Admin در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.admin` |
| نام | `Admin` |
| عنوان فارسی | پیشخان و CRUD Generator |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Admin` |
| کلاس موتور | `Tavaan\Engine\Admin\AdminEngine` |
| Facade | Admin (`admin`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Admin;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class AdminEngine extends BaseEngine
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

**فایل:** `src/AdminEngine.php`

## توضیحات

M8/TVN-0078: CRUD generator declarative (۲۷ نوع فیلد، validation خودکار، jalali_date دوطرفه، sanitizer برای richtext)، منوی پویا (manifest + permission filter)، ویجت/نمودار SVG داشبورد، RTL، theme override در resources/views/vendor/admin.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `view`, `rbac`, `orm`, `validation`, `http`

## Service Providerها

- `Tavaan\Engine\Admin\AdminManagerServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Admin.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
