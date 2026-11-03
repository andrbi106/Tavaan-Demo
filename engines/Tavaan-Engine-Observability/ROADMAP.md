# نقشه راه تکمیل Tavaan-Engine-Observability

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-067 |
| Engine ID | `tavaan.engine.observability` |
| Namespace | `Tavaan\Engine\Observability` |
| اولویت | **P1** — فاز M6 |
| وابستگی‌ها | `event`, `log`, `http` |
| Facade | `Metrics`, `Trace` |
| تخمین LOC | ۱۴۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/observability.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/observability.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 27 |
| کلاس‌ها | 27 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 3403 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 2 مورد (2 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `Metrics` (با 5 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_class | کلاس `Health` (با 4 متد عمومی) در پیاده‌سازی یافت نشد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Metrics` | `counter()`, `gauge()`, `histogram()`, `timer()`, `collect()` |
| `Tracer` | `startSpan()`, `currentSpan()`, `withSpan()` |
| `Health` | `add()`, `check()`, `live()`, `ready()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Counter/Gauge/Histogram + collect |
| `T-02` | Prometheus format |
| `T-03` | traceparent parse + inject |
| `T-04` | Span با exception |
| `T-05` | Health check up/down |
| `T-06` | Debug Bar فقط در dev |
| `T-07` | Sampling |

## ۶. معیارهای پذیرش

`AC-OBS-01`, `AC-OBS-08`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `Metrics` (با 5 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Metrics.php` با namespace صحیح.
2. پیاده‌سازی متدهای: counter, gauge, histogram, timer, collect.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_class

**شرح:** کلاس `Health` (با 4 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Health.php` با namespace صحیح.
2. پیاده‌سازی متدهای: add, check, live, ready.
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
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*