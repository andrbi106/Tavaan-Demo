# نقشه راه تکمیل Tavaan-Engine-Queue

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-061 |
| Engine ID | `tavaan.engine.queue` |
| Namespace | `Tavaan\Engine\Queue` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `database`, `cache`, `event`, `log` |
| Facade | `Queue` |
| تخمین LOC | ۲۲۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/queue.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/queue.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 48 |
| کلاس‌ها | 48 |
| فایل‌های تست | 14 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 5049 |

## ۳. شکاف بین مستندات و پیاده‌سازی

✅ هیچ شکاف قابل‌تشخیصی بین مستندات و پیاده‌سازی یافت نشد.

پیاده‌سازی با قواعد R6 (۷ قلم الزامی هر موتور) و API عمومی تعریف‌شده در مستندات همخوان است.

### توصیه‌ها برای نگهداری

- پوشش تست را به‌روز نگه دارید؛ هر بار که متدی به مستندات اضافه می‌شود، تست متناظر را اضافه کنید.
- با هر تغییر API عمومی، نسخهٔ `module.json` را bump کنید.
- در صورت تغییر رفتار، بلوک YAML ماشین‌خوان مستندات را به‌روز کنید.
## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `QueueManager` | `push()`, `later()`, `pushOn()`, `batch()`, `chain()`, `size()`, `connection()` |
| `Worker` | `run()`, `stop()`, `daemon()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | push/pop در هر ۵ درایور |
| `T-02` | retry با backoff نمایی |
| `T-03` | max tries → failed_jobs |
| `T-04` | delayed job (later) |
| `T-05` | Batch: همه/all/finally |
| `T-06` | Chain: ترتیب + شکست |
| `T-07` | Graceful shutdown با signal |
| `T-08` | Timeout → JobTimedOutException |
| `T-09` | Pop اتمیک با ۲ worker (SKIP LOCKED) |
| `T-10` | Serialization: مدل فقط کلید |
| `T-11` | Allowlist job class |
| `T-12` | Memory recycle |

## ۶. معیارهای پذیرش

`AC-QUEUE-01`, `AC-QUEUE-13`

## ۷. درایورها

`sync`, `database`, `redis`, `file`, `null`

## ۸. جداول دیتابیس

- `tvn_jobs`
- `tvn_failed_jobs`
- `tvn_job_batches`

## ۹. نقشه راه تکمیل (Worklog)

✅ نقشه راه اضافی لازم نیست؛ پیاده‌سازی با مستندات همخوان است.

توصیه می‌شود در مراحل بعدی:

1. افزودن benchmarkهای رسمی برای اهداف کارایی مستندشده.
2. تدوین README با مثال‌های کاربردی.
3. افزودن CI workflow برای اجرای خودکار تست‌ها در هر PR.
## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*