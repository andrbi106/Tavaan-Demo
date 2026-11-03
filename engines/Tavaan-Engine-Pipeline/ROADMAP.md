# نقشه راه تکمیل Tavaan-Engine-Pipeline

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-034 |
| Engine ID | `tavaan.engine.pipeline` |
| Namespace | `Tavaan\Engine\Pipeline` |
| اولویت | **P0** — فاز M1 |
| وابستگی‌ها | `container` |
| Facade | `ندارد (مستقیم استفاده می‌شود)` |
| تخمین LOC | ۲۵۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/pipeline.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/pipeline.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 5 |
| کلاس‌ها | 5 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 294 |

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
| `Pipeline` | `send()`, `through()`, `pipe()`, `via()`, `then()`, `thenReturn()`, `after()`, `terminate()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | ترتیب اجرا (onion): before/after در هر لایه |
| `T-02` | پارامتر `throttle:60,1` → `['60','1']` |
| `T-03` | Closure به‌عنوان pipe |
| `T-04` | short-circuit: pipe بدون `$next` → زنجیره قطع |
| `T-05` | exception در pipe میانی → propagate + terminate اجرا شد |
| `T-06` | `thenReturn()` = identity |
| `T-07` | Passable دلخواه (string, array, DTO) |
| `T-08` | `max_depth` → خطا |
| `T-09` | Alias نامعتبر → `PipelinePipeNotFoundException` |

## ۶. معیارهای پذیرش

`AC-PIP-01`, `AC-PIP-02`, `AC-PIP-03`, `AC-PIP-04`

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