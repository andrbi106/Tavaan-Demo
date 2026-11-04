# نقشه راه تکمیل Tavaan-Engine-Schedule

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-062 |
| Engine ID | `tavaan.engine.schedule` |
| Namespace | `Tavaan\Engine\Schedule` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `console`, `cache`, `queue` |
| Facade | `Schedule` |
| تخمین LOC | ۷۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/schedule.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/schedule.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 7 |
| کلاس‌ها | 7 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1326 |

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
| `ScheduleManager` | `command()`, `job()`, `call()`, `exec()`, `events()` |
| `CronExpression` | `isDue()`, `nextRunDates()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Cron parser: `*/5 * * * *`, `0 3 * * *`, `0 0 1 * *` |
| `T-02` | `isDue` در مرزها |
| `T-03` | Fluent: `daily()->at('03:00')` |
| `T-04` | Mutex: دو اجرای همزمان → یکی |
| `T-05` | onOneServer: دو سرور → یکی |
| `T-06` | timezone |
| `T-07` | nextRunDates |

## ۶. معیارهای پذیرش

`AC-SCH-01`, `AC-SCH-08`

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