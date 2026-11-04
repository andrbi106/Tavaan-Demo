# Tavaan Engine — Report

> **گزارش‌ساز** — موتور Report در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.report` |
| نام | `Report` |
| عنوان فارسی | گزارش‌ساز |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Report` |
| کلاس موتور | `Tavaan\Engine\Report\ReportEngine` |
| Facade | Report (`report`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Report;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ReportEngine extends BaseEngine
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

**فایل:** `src/ReportEngine.php`

## توضیحات

M7 — Dataset (Closure/QueryBuilder/SQL/آرایه) + فیلترهای allowlist، Group/Subtotal، Pivot، Aggregate با Decimal (بدون float — ADR-0013)، تاریخ جلالی، خروجی CSV (BOM+delimiter «;»+streaming+🔒 ضد CSV Injection)، xlsx خودنوشته OOXML با ZipWriterPure (بدون PhpSpreadsheet/ext-zip)، HTML با قالب report::table + fallback escaped، JSON، PDF اختیاری، 🔒 sandbox مسیر export و max_rows.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

**موتورهای اختیاری:** `database`, `filesystem`, `view`, `i18n`, `compat`, `http`

## Service Providerها

- `Tavaan\Engine\Report\ReportBuilderServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Report.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
