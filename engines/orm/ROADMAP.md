# نقشه راه تکمیل Tavaan-Engine-ORM

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-050 |
| Engine ID | `tavaan.engine.orm` |
| Namespace | `Tavaan\Engine\Database\ORM` |
| اولویت | **P0** — فاز M3 |
| وابستگی‌ها | `database`, `event`, `i18n` |
| Facade | `Model` (base class)` |
| تخمین LOC | ۴۰۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/orm.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/orm.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 0 |
| کلاس‌ها | 0 |
| فایل‌های تست | 3 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 0 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 1 مورد (1 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `Model` (با 25 متد عمومی) در پیاده‌سازی یافت نشد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Model` | `find()`, `findOrFail()`, `all()`, `where()`, `query()`, `create()`, `updateOrCreate()`, `firstOrCreate()`, `save()`, `delete()`, `restore()`, `forceDelete()`, `fill()`, `update()`, `refresh()`, `toArray()`, `toJson()`, `with()`, `withCount()`, `load()`, `observe()`, `addGlobalScope()`, `chunk()`, `lazy()`, `paginate()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | CRUD کامل با fillable |
| `T-02` | Mass Assignment: فیلد guarded → رد |
| `T-03` | هر ۱۱ نوع رابطه |
| `T-04` | Eager vs Lazy: شمارش کوئری (N+1 رفع شده) |
| `T-05` | Cast: money، enum، datetime، encrypted، json |
| `T-06` | Accessor/Mutator |
| `T-07` | Soft Delete + `withTrashed` |
| `T-08` | Global Scope (Tenant) |
| `T-09` | Observer: همهٔ رویدادها |
| `T-10` | `dispatchesEvents` → Domain Event |
| `T-11` | Repository pattern (بدون extends Model) |
| `T-12` | `hidden` در `toJson()` |
| `T-13` | `updateOrCreate`/`firstOrCreate` |
| `T-14` | Hydration ۱۰۰۰ رکورد ≤ ۲۵ms |

## ۶. معیارهای پذیرش

`AC-ORM-01`, `AC-ORM-02`, `AC-ORM-03`, `AC-ORM-04`, `AC-ORM-05`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `Model` (با 25 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Model.php` با namespace صحیح.
2. پیاده‌سازی متدهای: find, findOrFail, all, where, query, create, updateOrCreate, firstOrCreate, save, delete, restore, forceDelete, fill, update, refresh, toArray, toJson, with, withCount, load, observe, addGlobalScope, chunk, lazy, paginate.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*