# نقشه راه تکمیل Tavaan-Engine-Audit

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-058 |
| Engine ID | `tavaan.engine.audit` |
| Namespace | `Tavaan\Engine\Audit` |
| اولویت | **P1** — فاز M4 |
| وابستگی‌ها | `event`, `database`, `log`, `crypt` |
| Facade | `Audit` |
| تخمین LOC | ۷۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/audit.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/audit.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 10 |
| کلاس‌ها | 10 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 809 |

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
| `Auditor` | `record()`, `verify()`, `export()`, `prune()`, `lastHash()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | record با hash chain درست |
| `T-02` | دستکاری یک رکورد → `verify` fail |
| `T-03` | حذف یک رکورد → `verify` fail |
| `T-04` | Observer خودکار روی Model |
| `T-05` | `exclude_fields` mask می‌شود |
| `T-06` | Queue mode async |

## ۶. معیارهای پذیرش

`AC-AUD-01`, `AC-AUD-07`

## ۸. جداول دیتابیس

- `tvn_audit_logs`

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