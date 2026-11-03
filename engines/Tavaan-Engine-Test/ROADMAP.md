# نقشه راه تکمیل Tavaan-Engine-Test

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-084E |
| Engine ID | `tavaan.engine.test` |
| Namespace | `Tavaan\Testing` |
| اولویت | **P0** — فاز M8 (اما از M1 استفاده می‌شود) |
| وابستگی‌ها | همهٔ موتورها |
| Facade | `—` |
| تخمین LOC | ۲۵۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/test.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/test.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 22 |
| کلاس‌ها | 22 |
| فایل‌های تست | 8 |
| فایل‌های پیکربندی | 0 |
| migrationها | 0 |
| LOC واقعی | 3755 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 9 مورد (1 بالا / 8 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_config | هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد). |

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `expectException()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد). |
| 2 | missing_method | متد `get()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد). |
| 3 | missing_method | متد `post()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد). |
| 4 | missing_method | متد `postJson()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد). |
| 5 | missing_method | متد `actingAs()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد). |
| 6 | missing_method | متد `...()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد). |
| 7 | missing_method | متد `suite()` در کلاس `TestRunner` یافت نشد (پیاده‌سازی فعلی: 4 متد). |
| 8 | missing_method | متد `result()` در کلاس `TestRunner` یافت نشد (پیاده‌سازی فعلی: 4 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `TestCase` | `assertTrue()`, `assertSame()`, `assertEquals()`, `expectException()`, `get()`, `post()`, `postJson()`, `actingAs()`, `...()` |
| `TestRunner` | `run()`, `suite()`, `result()` |
| `Mock` | `of()` |
| `Factory` | `definition()`, `create()`, `make()`, `count()`, `state()` |

## ۶. معیارهای پذیرش

`AC-TEST-01`, `AC-TEST-02`, `AC-TEST-03`, `AC-TEST-04`, `AC-TEST-05`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_config

**شرح:** هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد).

**گام‌ها:**
1. ایجاد `config/<engine>.php` با همهٔ کلیدهای مستندشده.
2. افزودن به `config` در `module.json`.
3. تست با `php bin/tvn env:check`.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🟡 — missing_method

**شرح:** متد `expectException()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `expectException`.
3. افزودن تست Unit.

### TASK-03 🟡 — missing_method

**شرح:** متد `get()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `get`.
3. افزودن تست Unit.

### TASK-04 🟡 — missing_method

**شرح:** متد `post()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `post`.
3. افزودن تست Unit.

### TASK-05 🟡 — missing_method

**شرح:** متد `postJson()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `postJson`.
3. افزودن تست Unit.

### TASK-06 🟡 — missing_method

**شرح:** متد `actingAs()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `actingAs`.
3. افزودن تست Unit.

### TASK-07 🟡 — missing_method

**شرح:** متد `...()` در کلاس `TestCase` یافت نشد (پیاده‌سازی فعلی: 34 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `...`.
3. افزودن تست Unit.

### TASK-08 🟡 — missing_method

**شرح:** متد `suite()` در کلاس `TestRunner` یافت نشد (پیاده‌سازی فعلی: 4 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `suite`.
3. افزودن تست Unit.

### TASK-09 🟡 — missing_method

**شرح:** متد `result()` در کلاس `TestRunner` یافت نشد (پیاده‌سازی فعلی: 4 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `result`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*