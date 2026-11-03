# نقشه راه تکمیل Tavaan-Engine-Console

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-076 |
| Engine ID | `tavaan.engine.console` |
| Namespace | `Tavaan\Engine\Console` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `container`, `event`, `config` |
| Facade | `ندارد (Entry: `bin/tvn`)` |
| تخمین LOC | ۱۶۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/console.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/console.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 31 |
| کلاس‌ها | 31 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 3359 |

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
| `Command` | `handle()`, `argument()`, `option()`, `info()`, `error()`, `warn()`, `line()`, `table()`, `ask()`, `confirm()`, `choice()`, `progressStart()` |
| `ConsoleKernel` | `handle()`, `call()`, `register()`, `commands()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Signature parsing (argument/option/flag/array) |
| `T-02` | خروجی رنگی + `NO_COLOR` |
| `T-03` | Table/Progress/Ask با Input Fake |
| `T-04` | Command discovery + cache |
| `T-05` | کد خروج درست |
| `T-06` | `--force` bypass تأیید |
| `T-07` | Nested command (module:install) |

## ۶. معیارهای پذیرش

`AC-CLI-01`, `AC-CLI-02`, `AC-CLI-03`, `AC-CLI-04`

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
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:34:08 UTC تولید شده است.*