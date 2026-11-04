# نقشه راه تکمیل Tavaan-Engine-Feature-Flag

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-075 |
| Engine ID | `tavaan.engine.feature-flag` |
| Namespace | `Tavaan\Engine\FeatureFlag` |
| اولویت | **P2** — فاز M6 |
| وابستگی‌ها | `cache`, `rbac` |
| Facade | `Feature` |
| تخمین LOC | ۶۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/feature-flag.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/feature-flag.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 5 |
| کلاس‌ها | 5 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 667 |

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
| `FeatureManager` | `enabled()`, `for()`, `value()`, `define()`, `kill()`, `revive()`, `experiment()`, `percentage()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | enabled/disabled |
| `T-02` | role/user/tenant targeting |
| `T-03` | percentage پایدار (همان کاربر = همان نتیجه) |
| `T-04` | Kill Switch |
| `T-05` | A/B variant پایدار |
| `T-06` | cache |

## ۶. معیارهای پذیرش

`AC-FF-01`

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