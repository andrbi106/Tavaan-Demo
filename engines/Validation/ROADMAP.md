# نقشه راه تکمیل Tavaan-Engine-Validation

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-051 |
| Engine ID | `tavaan.engine.validation` |
| Namespace | `Tavaan\Engine\Validation` |
| اولویت | **P0** — فاز M3 |
| وابستگی‌ها | `i18n`, `database`, `config` |
| Facade | `Validator` |
| تخمین LOC | ۲۲۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/validation.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/validation.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 25 |
| کلاس‌ها | 25 |
| فایل‌های تست | 3 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 3108 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 3 مورد (0 بالا / 3 متوسط / 0 پایین).

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `make()` در کلاس `Validator` یافت نشد (پیاده‌سازی فعلی: 12 متد). |
| 2 | missing_method | متد `replacer()` در کلاس `Validator` یافت نشد (پیاده‌سازی فعلی: 12 متد). |
| 3 | missing_method | متد `forDto()` در کلاس `Validator` یافت نشد (پیاده‌سازی فعلی: 12 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Validator` | `make()`, `validate()`, `extend()`, `replacer()`, `forDto()`, `sometimes()` |
| `FormRequest` | `authorize()`, `rules()`, `messages()`, `attributes()`, `validated()`, `validateResolved()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | هر ۸۵ قاعده: پاس و فیل |
| `T-02` | `iran_national_code` با ۲۰ نمونهٔ معتبر/نامعتبر |
| `T-03` | `iran_sheba`/`iran_card_number` با checksum |
| `T-04` | `unique` با ignore |
| `T-05` | Wildcard `items.*.qty` |
| `T-06` | FormRequest: authorize fail → 403 |
| `T-07` | پیام سفارشی + attribute سفارشی |
| `T-08` | `validated()` فقط فیلدهای قاعده‌دار |
| `T-09` | قاعدهٔ سفارشی (RuleInterface) |
| `T-10` | Attribute-based روی DTO |
| `T-11` | ReDoS regex → رد |
| `T-12` | ترجمهٔ فارسی/انگلیسی |

## ۶. معیارهای پذیرش

`AC-VAL-01`, `AC-VAL-02`, `AC-VAL-03`, `AC-VAL-04`, `AC-VAL-05`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🟡 — missing_method

**شرح:** متد `make()` در کلاس `Validator` یافت نشد (پیاده‌سازی فعلی: 12 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `make`.
3. افزودن تست Unit.

### TASK-02 🟡 — missing_method

**شرح:** متد `replacer()` در کلاس `Validator` یافت نشد (پیاده‌سازی فعلی: 12 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `replacer`.
3. افزودن تست Unit.

### TASK-03 🟡 — missing_method

**شرح:** متد `forDto()` در کلاس `Validator` یافت نشد (پیاده‌سازی فعلی: 12 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `forDto`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*