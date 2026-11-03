# نقشه راه تکمیل Tavaan-Engine-Hash

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-055 |
| Engine ID | `tavaan.engine.hash` |
| Namespace | `Tavaan\Engine\Hash` |
| اولویت | **P0** — فاز M4 |
| وابستگی‌ها | `config` |
| Facade | `Hash` |
| تخمین LOC | ۳۵۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/hash.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/hash.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 4 |
| کلاس‌ها | 4 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 233 |

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
| `HashManager` | `make()`, `check()`, `needsRehash()`, `info()`, `driver()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | make/check roundtrip |
| `T-02` | رمز اشتباه → false |
| `T-03` | needsRehash با cost قدیمی → true |
| `T-04` | Argon2id نبود → bcrypt fallback |
| `T-05` | دو hash از یک رمز متفاوت (salt) |

## ۶. معیارهای پذیرش

`AC-HASH-01`, `AC-HASH-06`

## ۷. درایورها

`argon2id`, `bcrypt`

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