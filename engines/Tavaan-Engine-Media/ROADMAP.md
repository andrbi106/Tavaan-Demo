# نقشه راه تکمیل Tavaan-Engine-Media

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-074 |
| Engine ID | `tavaan.engine.media` |
| Namespace | `Tavaan\Engine\Media` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `filesystem`, `crypt`, `validation` |
| Facade | `Media` |
| تخمین LOC | ۱۵۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/media.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/media.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 8 |
| کلاس‌ها | 8 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 1495 |

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
| `MediaManager` | `upload()`, `fromPath()`, `variants()` |
| `Image` | `make()`, `resize()`, `crop()`, `thumbnail()`, `watermark()`, `convert()`, `rotate()`, `flip()`, `save()`, `exif()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | آپلود تصویر معتبر |
| `T-02` | `.php` با image MIME → رد |
| `T-03` | magic bytes detection |
| `T-04` | resize/crop/thumbnail (با gd) |
| `T-05` | WebP/AVIF |
| `T-06` | watermark |
| `T-07` | image bomb → رد |
| `T-08` | بدون gd → EngineCapabilityException |
| `T-09` | dedup با hash |
| `T-10` | EXIF |

## ۶. معیارهای پذیرش

`AC-MEDIA-01`, `AC-MEDIA-11`

## ۸. جداول دیتابیس

- `tvn_media`

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