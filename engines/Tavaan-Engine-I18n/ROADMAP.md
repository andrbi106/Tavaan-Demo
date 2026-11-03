# نقشه راه تکمیل Tavaan-Engine-I18n

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-069 |
| Engine ID | `tavaan.engine.i18n` |
| Namespace | `Tavaan\Engine\I18n` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `config`, `cache` |
| Facade | `Lang` |
| تخمین LOC | ۹۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/i18n.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/i18n.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 7 |
| کلاس‌ها | 7 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1083 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 3 مورد (0 بالا / 3 متوسط / 0 پایین).

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `namespace()` در کلاس `Translator` یافت نشد (پیاده‌سازی فعلی: 11 متد). |
| 2 | missing_method | متد `currency()` در کلاس `PersianFormatter` یافت نشد (پیاده‌سازی فعلی: 4 متد). |
| 3 | missing_method | متد `date()` در کلاس `PersianFormatter` یافت نشد (پیاده‌سازی فعلی: 4 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Translator` | `get()`, `has()`, `choice()`, `setLocale()`, `getLocale()`, `namespace()`, `addNamespace()` |
| `PersianFormatter` | `digits()`, `number()`, `currency()`, `date()`, `toLatinDigits()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | `trans()` با namespace |
| `T-02` | placeholder replace |
| `T-03` | pluralization fa/en/ar/ru |
| `T-04` | fallback chain |
| `T-05` | Accept-Language negotiation |
| `T-06` | `PersianFormatter::number/currency` |
| `T-07` | `toLatinDigits` |
| `T-08` | کلید گمشده → log (نه crash) |
| `T-09` | RTL detection |

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🟡 — missing_method

**شرح:** متد `namespace()` در کلاس `Translator` یافت نشد (پیاده‌سازی فعلی: 11 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `namespace`.
3. افزودن تست Unit.

### TASK-02 🟡 — missing_method

**شرح:** متد `currency()` در کلاس `PersianFormatter` یافت نشد (پیاده‌سازی فعلی: 4 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `currency`.
3. افزودن تست Unit.

### TASK-03 🟡 — missing_method

**شرح:** متد `date()` در کلاس `PersianFormatter` یافت نشد (پیاده‌سازی فعلی: 4 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `date`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*