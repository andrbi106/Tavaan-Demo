# نقشه راه تکمیل Tavaan-Engine-View

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-043 |
| Engine ID | `tavaan.engine.view` |
| Namespace | `Tavaan\Engine\View` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `event`, `filesystem`, `i18n`؛ نرم: `rbac`, `http` |
| Facade | `View` |
| تخمین LOC | ۱۸۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/view.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/view.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 12 |
| کلاس‌ها | 12 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1679 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 9 مورد (0 بالا / 9 متوسط / 0 پایین).

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `composer()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد). |
| 2 | missing_method | متد `component()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد). |
| 3 | missing_method | متد `renderEach()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد). |
| 4 | missing_method | متد `file()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد). |
| 5 | missing_method | متد `first()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد). |
| 6 | missing_method | متد `withData()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد). |
| 7 | missing_method | متد `nest()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد). |
| 8 | missing_method | متد `extends()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد). |
| 9 | missing_method | متد `section()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `ViewFactory` | `make()`, `share()`, `exists()`, `addNamespace()`, `addLocation()`, `directive()`, `composer()`, `component()`, `renderEach()`, `file()`, `first()` |
| `View` | `render()`, `with()`, `withData()`, `getData()`, `nest()`, `extends()`, `section()` |
| `Escaper` | `html()`, `attr()`, `js()`, `url()`, `css()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | `{{ }}` روی `<script>` → escaped |
| `T-02` | `{!! !!}` → raw |
| `T-03` | `@if/@elseif/@else/@endif` ترکیبی |
| `T-04` | `@foreach/@empty/@endforelse` |
| `T-05` | `@extends` + `@section` + `@yield` |
| `T-06` | Nested layout (۳ سطح) |
| `T-07` | `@push`/`@stack` با ترتیب صحیح |
| `T-08` | `@include` با namespace و override |
| `T-09` | `<x-alert>` با props و slot |
| `T-10` | Component class-based |
| `T-11` | `@can` با Policy |
| `T-12` | `@module` HMVC با depth limit |
| `T-13` | Compile cache: تغییر template → invalidate |
| `T-14` | Directive نامعتبر → `TemplateCompilationException` |
| `T-15` | Recursive include → خطا در max_depth |
| `T-16` | `@json` با دادهٔ فارسی → Unicode سالم |
| `T-17` | `@verbatim` → بدون پردازش |
| `T-18` | XSS Fuzzing: ۲۰۰ payload |

## ۶. معیارهای پذیرش

`AC-VIEW-01`, `AC-VIEW-02`, `AC-VIEW-03`, `AC-VIEW-04`, `AC-VIEW-05`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🟡 — missing_method

**شرح:** متد `composer()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `composer`.
3. افزودن تست Unit.

### TASK-02 🟡 — missing_method

**شرح:** متد `component()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `component`.
3. افزودن تست Unit.

### TASK-03 🟡 — missing_method

**شرح:** متد `renderEach()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `renderEach`.
3. افزودن تست Unit.

### TASK-04 🟡 — missing_method

**شرح:** متد `file()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `file`.
3. افزودن تست Unit.

### TASK-05 🟡 — missing_method

**شرح:** متد `first()` در کلاس `ViewFactory` یافت نشد (پیاده‌سازی فعلی: 44 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `first`.
3. افزودن تست Unit.

### TASK-06 🟡 — missing_method

**شرح:** متد `withData()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `withData`.
3. افزودن تست Unit.

### TASK-07 🟡 — missing_method

**شرح:** متد `nest()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `nest`.
3. افزودن تست Unit.

### TASK-08 🟡 — missing_method

**شرح:** متد `extends()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `extends`.
3. افزودن تست Unit.

### TASK-09 🟡 — missing_method

**شرح:** متد `section()` در کلاس `View` یافت نشد (پیاده‌سازی فعلی: 6 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `section`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*