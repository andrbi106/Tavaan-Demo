# نقشه راه تکمیل Tavaan-Engine-API

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-044 |
| Engine ID | `tavaan.engine.api` |
| Namespace | `Tavaan\Engine\Api` |
| اولویت | **P1** — فاز M6 |
| وابستگی‌ها | `http`, `validation`, `orm` |
| Facade | `ندارد (Resource base class)` |
| تخمین LOC | ۱۲۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/api.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/api.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 17 |
| کلاس‌ها | 17 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 2107 |

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
| `JsonResource` | `make()`, `collection()`, `toArray()`, `when()`, `whenLoaded()`, `whenCounted()`, `mergeWhen()`, `response()` |
| `OpenApiGenerator` | `generate()`, `toYaml()`, `toJson()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Resource با nested + whenLoaded |
| `T-02` | Collection با pagination meta |
| `T-03` | Versioning url/header |
| `T-04` | OpenAPI generation از routes |
| `T-05` | Idempotency: دو POST با یک key → یک اثر |
| `T-06` | Rate limit → 429 + headers |
| `T-07` | `when()` فیلد مخفی برای guest |

## ۶. معیارهای پذیرش

`AC-API-01`, `AC-API-08`

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
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:33:03 UTC تولید شده است.*