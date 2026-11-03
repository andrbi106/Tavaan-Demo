# نقشه راه تکمیل Tavaan-Engine-Tenancy

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-082E |
| Engine ID | `tavaan.engine.tenancy` |
| Namespace | `Tavaan\Engine\Tenancy` |
| اولویت | **P1** — فاز M6 |
| وابستگی‌ها | `database`, `cache`, `http`, `event` |
| Facade | `Tenant` |
| تخمین LOC | ۱۰۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/tenancy.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/tenancy.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 40 |
| کلاس‌ها | 40 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 3319 |

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
| `TenantManager` | `current()`, `id()`, `identifier()`, `run()`, `withoutCurrentScope()`, `create()`, `findByDomain()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | resolve از domain/header/path/token |
| `T-02` | Row-Level isolation |
| `T-03` | Cache/Queue/FS isolation |
| `T-04` | Provisioning (استراتژی B) |
| `T-05` | Quota |
| `T-06` | Resident context cleanup |
| `T-07` | Cross-tenant leakage fuzzing |

## ۶. معیارهای پذیرش

`AC-TEN-01`

## ۸. جداول دیتابیس

- `tvn_tenants`

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