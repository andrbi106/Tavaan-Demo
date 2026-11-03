# نقشه راه تکمیل Tavaan-Engine-Log

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-036 |
| Engine ID | `tavaan.engine.log` |
| Namespace | `Tavaan\Engine\Log` |
| اولویت | **P0** — فاز M1 |
| وابستگی‌ها | `config`؛ نرم: `event`, `queue` |
| Facade | `Log` |
| تخمین LOC | ۹۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/log.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/log.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 20 |
| کلاس‌ها | 20 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1426 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 2 مورد (1 بالا / 1 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_migrations | مستندات 1 جدول تعریف می‌کند (logs) اما هیچ فایل migration در `migrations/` وجود ندارد. |

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `driver()` در کلاس `LogManager` یافت نشد (پیاده‌سازی فعلی: 19 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `LogManager` | `channel()`, `stack()`, `driver()`, `extend()`, `flush()`, `getChannels()` |
| `Logger` | `emergency()`, `alert()`, `critical()`, `error()`, `warning()`, `notice()`, `info()`, `debug()`, `log()`, `withContext()`, `shareContext()`, `withoutContext()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | هر ۸ سطح → severity RFC 5424 درست |
| `T-02` | سطح فیلتر: `level=warning` → `info` نوشته نمی‌شود |
| `T-03` | Stack channel → هر دو کانال |
| `T-04` | Mask: `password` → `********` |
| `T-05` | Pattern mask: شمارهٔ کارت ۱۶ رقمی → `4321********5678` |
| `T-06` | چرخش روزانه با Clock Fake |
| `T-07` | `days=3` → حذف فایل‌های قدیمی‌تر |
| `T-08` | Buffer: ۵۰ رکورد → یک `writeBatch` |
| `T-09` | `shutdown` → flush |
| `T-10` | Log Injection با `\r\n` → پاک شده |
| `T-11` | `per_request_limit` → پس از حد، فقط یک هشدار |
| `T-12` | JSON formatter → UTF-8 فارسی بدون escape |
| `T-13` | Context تجمعی (`shareContext`) در همهٔ رکوردها |

## ۶. معیارهای پذیرش

`AC-LOG-01`, `AC-LOG-02`, `AC-LOG-03`, `AC-LOG-04`, `AC-LOG-05`

## ۸. جداول دیتابیس

- `logs`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_migrations

**شرح:** مستندات 1 جدول تعریف می‌کند (logs) اما هیچ فایل migration در `migrations/` وجود ندارد.

**گام‌ها:**
1. ایجاد فایل `migrations/<date>_create_<table>_tables.php` برای هر جدول.
2. تعریف ستون‌ها بر اساس مستندات.
3. افزودن نام فایل به `migrations` در `module.json`.
4. تست اجرای migration روی دیتابیس درون‌حافظه‌ای.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🟡 — missing_method

**شرح:** متد `driver()` در کلاس `LogManager` یافت نشد (پیاده‌سازی فعلی: 19 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `driver`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*