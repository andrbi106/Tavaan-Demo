# نقشه راه تکمیل Tavaan-Engine-Migration

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-049 |
| Engine ID | `tavaan.engine.migration` |
| Namespace | `Tavaan\Engine\Database\Migration` |
| اولویت | **P0** — فاز M3 |
| وابستگی‌ها | `schema`, `database` |
| Facade | `ندارد (CLI)` |
| تخمین LOC | ۸۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/migration.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/migration.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 6 |
| کلاس‌ها | 6 |
| فایل‌های تست | 5 |
| فایل‌های پیکربندی | 0 |
| migrationها | 0 |
| LOC واقعی | 272 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 4 مورد (3 بالا / 0 متوسط / 1 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `Migrator` (با 6 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_migrations | مستندات 1 جدول تعریف می‌کند (tvn_migrations) اما هیچ فایل migration در `migrations/` وجود ندارد. |
| 3 | missing_config | هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد). |

### ۳٫۳ شکاف‌های با اولویت پایین 🟢

| # | نوع | شرح |
|---|------|------|
| 1 | loc_underestimate | حجم پیاده‌سازی (272 خط) به‌طور قابل‌توجهی کمتر از تخمین مستندات (800 خط) است. احتمالاً بخش‌هایی از قابلیت‌ها هنوز پیاده‌سازی نشده‌اند. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Migrator` | `run()`, `rollback()`, `refresh()`, `fresh()`, `status()`, `install()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | migrate → up اجرا، در repository ثبت |
| `T-02` | rollback → down، ترتیب معکوس |
| `T-03` | Lock: دو migrator همزمان → یکی صبر/خطا |
| `T-04` | dry-run بدون تغییر DB |
| `T-05` | batch numbering |
| `T-06` | migrate:fresh در prod بدون force → رد |
| `T-07` | Expand-Contract روی داده |

## ۶. معیارهای پذیرش

`AC-MIG-01`, `AC-MIG-08`

## ۸. جداول دیتابیس

- `tvn_migrations`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `Migrator` (با 6 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Migrator.php` با namespace صحیح.
2. پیاده‌سازی متدهای: run, rollback, refresh, fresh, status, install.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_migrations

**شرح:** مستندات 1 جدول تعریف می‌کند (tvn_migrations) اما هیچ فایل migration در `migrations/` وجود ندارد.

**گام‌ها:**
1. ایجاد فایل `migrations/<date>_create_<table>_tables.php` برای هر جدول.
2. تعریف ستون‌ها بر اساس مستندات.
3. افزودن نام فایل به `migrations` در `module.json`.
4. تست اجرای migration روی دیتابیس درون‌حافظه‌ای.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-03 🔴 — missing_config

**شرح:** هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد).

**گام‌ها:**
1. ایجاد `config/<engine>.php` با همهٔ کلیدهای مستندشده.
2. افزودن به `config` در `module.json`.
3. تست با `php bin/tvn env:check`.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-04 🟢 — loc_underestimate

**شرح:** حجم پیاده‌سازی (272 خط) به‌طور قابل‌توجهی کمتر از تخمین مستندات (800 خط) است. احتمالاً بخش‌هایی از قابلیت‌ها هنوز پیاده‌سازی نشده‌اند.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*