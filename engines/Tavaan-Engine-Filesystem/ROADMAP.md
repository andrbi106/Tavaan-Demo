# نقشه راه تکمیل Tavaan-Engine-Filesystem

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-063 |
| Engine ID | `tavaan.engine.filesystem` |
| Namespace | `Tavaan\Engine\Filesystem` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `config`, `crypt`؛ نرم: `media` |
| Facade | `Storage` |
| تخمین LOC | ۱۲۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/filesystem.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/filesystem.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 13 |
| کلاس‌ها | 13 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 2554 |

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
| `FilesystemManager` | `disk()`, `get()`, `put()`, `exists()`, `delete()`, `copy()`, `move()`, `size()`, `url()`, `temporaryUrl()`, `files()`, `allFiles()`, `directories()`, `makeDirectory()`, `deleteDirectory()`, `readStream()`, `writeStream()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | put/get/exists/delete در هر ۴ درایور |
| `T-02` | Path traversal `../../etc/passwd` → رد |
| `T-03` | Symlink escape → رد |
| `T-04` | Atomic write (بدون partial) |
| `T-05` | S3 signing با AWS test vectors |
| `T-06` | readStream حافظه ثابت |
| `T-07` | temporaryUrl امضاشده |
| `T-08` | directories/files recursive |

## ۶. معیارهای پذیرش

`AC-FS-01`, `AC-FS-09`

## ۷. درایورها

`local`, `s3`, `ftp`, `memory`

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