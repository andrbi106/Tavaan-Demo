# نقشه راه تکمیل Tavaan-Engine-Module

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-038 |
| Engine ID | `tavaan.engine.module` |
| Namespace | `Tavaan\Engine\Module` |
| اولویت | **P0** — فاز M1 |
| وابستگی‌ها | `container`, `config` |
| Facade | `Module` |
| تخمین LOC | ۹۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/module.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/module.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 14 |
| کلاس‌ها | 14 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 0 |
| migrationها | 0 |
| LOC واقعی | 1695 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 5 مورد (2 بالا / 3 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `ModuleRunner` (با 1 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_config | هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد). |

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `install()` در کلاس `ModuleManager` یافت نشد (پیاده‌سازی فعلی: 4 متد). |
| 2 | missing_method | متد `uninstall()` در کلاس `ModuleManager` یافت نشد (پیاده‌سازی فعلی: 4 متد). |
| 3 | missing_method | متد `update()` در کلاس `ModuleManager` یافت نشد (پیاده‌سازی فعلی: 4 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `ModuleRegistry` | `discover()`, `all()`, `get()`, `sorted()`, `enabled()`, `dependencies()`, `dependentsOf()` |
| `ModuleManager` | `install()`, `uninstall()`, `enable()`, `disable()`, `update()`, `validate()` |
| `ModuleRunner` | `run()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | parse manifest معتبر/نامعتبر |
| `T-02` | Topological sort با ۵ ماژول |
| `T-03` | چرخه → exception با مسیر |
| `T-04` | install/enable/disable |
| `T-05` | disable با dependent → رد |
| `T-06` | cache manifest |
| `T-07` | `Module::run` (HMVC) |

## ۶. معیارهای پذیرش

`AC-MOD-01`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `ModuleRunner` (با 1 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/ModuleRunner.php` با namespace صحیح.
2. پیاده‌سازی متدهای: run.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_config

**شرح:** هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد).

**گام‌ها:**
1. ایجاد `config/<engine>.php` با همهٔ کلیدهای مستندشده.
2. افزودن به `config` در `module.json`.
3. تست با `php bin/tvn env:check`.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-03 🟡 — missing_method

**شرح:** متد `install()` در کلاس `ModuleManager` یافت نشد (پیاده‌سازی فعلی: 4 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `install`.
3. افزودن تست Unit.

### TASK-04 🟡 — missing_method

**شرح:** متد `uninstall()` در کلاس `ModuleManager` یافت نشد (پیاده‌سازی فعلی: 4 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `uninstall`.
3. افزودن تست Unit.

### TASK-05 🟡 — missing_method

**شرح:** متد `update()` در کلاس `ModuleManager` یافت نشد (پیاده‌سازی فعلی: 4 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `update`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*