# نقشه راه تکمیل Tavaan-Engine-Container

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-031 |
| Engine ID | `tavaan.engine.container` |
| Namespace | `Tavaan\Engine\Container` |
| اولویت | **P0** — فاز M1 (اولین موتور) |
| وابستگی‌ها | هیچ (فقط `core/Contracts`) |
| Facade | `App` |
| تخمین LOC | ? |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/container.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/container.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 14 |
| کلاس‌ها | 14 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1608 |

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
| `Container` | `bind()`, `singleton()`, `scoped()`, `factory()`, `instance()`, `alias()`, `tag()`, `tagged()`, `when()`, `extend()`, `make()`, `get()`, `has()`, `call()`, `lazy()`, `flush()`, `flushScoped()`, `forgetInstance()`, `bindings()`, `dependencyGraph()`, `stats()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Singleton دو بار make → یک نمونه |
| `T-02` | Transient دو بار → دو نمونه |
| `T-03` | Auto-wire ۵ سطح تو‌در‌تو |
| `T-04` | چرخه A→B→A |
| `T-05` | Union `(A&B)\ |
| `T-06` | پارامتر با default و بدون type + `strict.allow_untyped=false` |
| `T-07` | Contextual برای دو مصرف‌کننده |
| `T-08` | `tagged()` lazy (بدون make تا iterate) |
| `T-09` | `extend()` دو بار → زنجیرهٔ decorator |
| `T-10` | `flushScoped()` → singleton باقی، scoped حذف |
| `T-11` | Variadic parameter |
| `T-12` | Promoted readonly property |
| `T-13` | Enum injection با default |
| `T-14` | `call()` روی static method |
| `T-15` | Reflection Cache dump/load → نتیجه یکسان |

## ۶. معیارهای پذیرش

`AC-CON-01`, `AC-CON-02`, `AC-CON-03`, `AC-CON-04`, `AC-CON-05`, `AC-CON-06`

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