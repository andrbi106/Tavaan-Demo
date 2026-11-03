# نقشه راه تکمیل Tavaan-Engine-Schema

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-048 |
| Engine ID | `tavaan.engine.schema` |
| Namespace | `Tavaan\Engine\Database\Schema` |
| اولویت | **P0** — فاز M3 |
| وابستگی‌ها | `database` |
| Facade | `Schema` |
| تخمین LOC | ۱۱۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/schema.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/schema.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 0 |
| کلاس‌ها | 0 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 0 |
| migrationها | 0 |
| LOC واقعی | 0 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 3 مورد (3 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `SchemaBuilder` (با 10 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_class | کلاس `Blueprint` (با 10 متد عمومی) در پیاده‌سازی یافت نشد. |
| 3 | missing_config | هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `SchemaBuilder` | `create()`, `table()`, `drop()`, `dropIfExists()`, `rename()`, `hasTable()`, `hasColumn()`, `getColumns()`, `getTables()`, `introspect()` |
| `Blueprint` | `all()`, `column()`, `types()`, `primary()`, `unique()`, `index()`, `foreign()`, `fullText()`, `timestamps()`, `softDeletes()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | همهٔ انواع ستون → DDL درست در ۳ درایور |
| `T-02` | foreign key + onDelete |
| `T-03` | index/unique/fullText |
| `T-04` | `table()` (ALTER) با after |
| `T-05` | enum در ۳ درایور |
| `T-06` | introspect |

## ۶. معیارهای پذیرش

`AC-SCH-01`, `AC-SCH-07`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `SchemaBuilder` (با 10 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/SchemaBuilder.php` با namespace صحیح.
2. پیاده‌سازی متدهای: create, table, drop, dropIfExists, rename, hasTable, hasColumn, getColumns, getTables, introspect.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_class

**شرح:** کلاس `Blueprint` (با 10 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Blueprint.php` با namespace صحیح.
2. پیاده‌سازی متدهای: all, column, types, primary, unique, index, foreign, fullText, timestamps, softDeletes.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

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

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*