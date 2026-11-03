# نقشه راه تکمیل Tavaan-Engine-Error

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-035 |
| Engine ID | `tavaan.engine.error` |
| Namespace | `Tavaan\Engine\Error` |
| اولویت | **P0** — فاز M1 |
| وابستگی‌ها | `config`, `log`, `event` |
| Facade | `ندارد (Kernel-level)` |
| تخمین LOC | ۸۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/error.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/error.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 10 |
| کلاس‌ها | 10 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1101 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 1 مورد (1 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `LogMasker` (با 4 متد عمومی) در پیاده‌سازی یافت نشد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `ExceptionHandler` | `report()`, `render()`, `map()`, `renderable()`, `reportable()`, `dontReport()`, `level()`, `context()` |
| `ErrorHandler` | `register()`, `unregister()`, `handleError()`, `handleException()`, `handleShutdown()` |
| `LogMasker` | `mask()`, `maskString()`, `addKeys()`, `addPatterns()` |

## ۶. معیارهای پذیرش

`AC-ERR-01`, `AC-ERR-09`, `AC-ERR-10`, `AC-ERR-11`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `LogMasker` (با 4 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/LogMasker.php` با namespace صحیح.
2. پیاده‌سازی متدهای: mask, maskString, addKeys, addPatterns.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:34:08 UTC تولید شده است.*