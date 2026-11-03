# نقشه راه تکمیل Tavaan-Engine-Report

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-072 |
| Engine ID | `tavaan.engine.report` |
| Namespace | `Tavaan\Engine\Report` |
| اولویت | **P2** — فاز M7 |
| وابستگی‌ها | `database`, `filesystem`, `view` |
| Facade | `Report` |
| تخمین LOC | ۱۶۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/report.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/report.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 20 |
| کلاس‌ها | 20 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 2416 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 5 مورد (0 بالا / 5 متوسط / 0 پایین).

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `csv()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد). |
| 2 | missing_method | متد `xlsx()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد). |
| 3 | missing_method | متد `html()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد). |
| 4 | missing_method | متد `json()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد). |
| 5 | missing_method | متد `pdf()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `ReportBuilder` | `dataset()`, `addField()`, `addFilter()`, `groupBy()`, `pivot()`, `orderBy()`, `run()` |
| `ReportExporter` | `csv()`, `xlsx()`, `html()`, `json()`, `pdf()`, `stream()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Dataset + fields + filters |
| `T-02` | groupBy + subtotal |
| `T-03` | pivot |
| `T-04` | CSV export با BOM + delimiter |
| `T-05` | xlsx export → قابل باز شدن |
| `T-06` | CSV injection sanitize |
| `T-07` | streaming حافظه ثابت |
| `T-08` | max_rows limit |
| `T-09` | HTML render با view |

## ۶. معیارهای پذیرش

`AC-RPT-01`, `AC-RPT-10`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🟡 — missing_method

**شرح:** متد `csv()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `csv`.
3. افزودن تست Unit.

### TASK-02 🟡 — missing_method

**شرح:** متد `xlsx()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `xlsx`.
3. افزودن تست Unit.

### TASK-03 🟡 — missing_method

**شرح:** متد `html()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `html`.
3. افزودن تست Unit.

### TASK-04 🟡 — missing_method

**شرح:** متد `json()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `json`.
3. افزودن تست Unit.

### TASK-05 🟡 — missing_method

**شرح:** متد `pdf()` در کلاس `ReportExporter` یافت نشد (پیاده‌سازی فعلی: 3 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `pdf`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*