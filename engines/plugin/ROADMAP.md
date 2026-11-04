# نقشه راه تکمیل Tavaan-Engine-Plugin

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-079 |
| Engine ID | `tavaan.engine.plugin` |
| Namespace | `Tavaan\Engine\Plugin` |
| اولویت | **P1** — فاز M8 |
| وابستگی‌ها | `module`, `filesystem`, `crypt` |
| Facade | `Plugin` |
| تخمین LOC | ۹۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/plugin.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/plugin.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 20 |
| کلاس‌ها | 20 |
| فایل‌های تست | 5 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 2234 |

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
| `PluginManager` | `install()`, `uninstall()`, `enable()`, `disable()`, `update()`, `list()`, `verify()`, `scan()` |
| `Hook` | `listen()`, `fire()`, `has()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | verify امضا معتبر/نامعتبر |
| `T-02` | lock hash → دستکاری تشخیص |
| `T-03` | scan توابع خطرناک |
| `T-04` | capability enforcement (بدون DB) |
| `T-05` | hook listener |
| `T-06` | install/enable/disable |
| `T-07` | vendor ایزوله (بدون تداخل با هسته) |
| `T-08` | پلاگین با وابستگی بیرونی (vendor) → کار می‌کند (معافیت) |

## ۶. معیارهای پذیرش

`AC-PLG-01`, `AC-PLG-09`

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