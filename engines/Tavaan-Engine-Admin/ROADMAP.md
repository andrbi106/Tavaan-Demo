# نقشه راه تکمیل Tavaan-Engine-Admin

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-078 |
| Engine ID | `tavaan.engine.admin` |
| Namespace | `Tavaan\Engine\Admin` |
| اولویت | **P2** — فاز M8 |
| وابستگی‌ها | `view`, `rbac`, `orm`, `validation`, `http` |
| Facade | `Admin` |
| تخمین LOC | ۲۵۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/admin.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/admin.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 15 |
| کلاس‌ها | 15 |
| فایل‌های تست | 3 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 2526 |

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
| `AdminManager` | `resource()`, `menu()`, `dashboard()`, `widget()` |
| `Resource` | `title()`, `columns()`, `searchable()`, `filterable()`, `sortable()`, `form()`, `actions()`, `show()` |
| `Form` | `text()`, `textarea()`, `number()`, `money()`, `select()`, `relation()`, `media()`, `richtext()`, `jalaliDate()`, `repeater()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | CRUD generator → ۷ route |
| `T-02` | Permission روی هر عملیات |
| `T-03` | Form validation خودکار |
| `T-04` | Sanitizer در richtext |
| `T-05` | منو بر اساس مجوز |
| `T-06` | Widget داشبورد |
| `T-07` | jalali date field |
| `T-08` | RTL layout |

## ۶. معیارهای پذیرش

`AC-ADM-01`, `AC-ADM-02`, `AC-ADM-03`, `AC-ADM-04`

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