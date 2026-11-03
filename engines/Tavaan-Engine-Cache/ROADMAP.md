# نقشه راه تکمیل Tavaan-Engine-Cache

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-060 |
| Engine ID | `tavaan.engine.cache` |
| Namespace | `Tavaan\Engine\Cache` |
| اولویت | **P0** — فاز M4 |
| وابستگی‌ها | `config`, `event`؛ نرم: `database` |
| Facade | `Cache` |
| تخمین LOC | ۱۴۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/cache.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/cache.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 13 |
| کلاس‌ها | 13 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 1977 |

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
| `CacheManager` | `store()`, `driver()`, `extend()`, `tags()`, `lock()`, `getDefaultDriver()` |
| `CacheRepository` | `get()`, `put()`, `has()`, `missing()`, `forget()`, `flush()`, `forever()`, `remember()`, `rememberForever()`, `add()`, `increment()`, `decrement()`, `pull()`, `many()`, `putMany()`, `lock()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | همهٔ ۶ درایور: get/put/has/forget/flush |
| `T-02` | TTL انقضا (با Clock Fake) |
| `T-03` | Tag flush فقط تگ مربوطه |
| `T-04` | Lock: فقط یک فرایند acquire |
| `T-05` | Stampede: ۱۰۰ همزمان → ۱ بازسازی |
| `T-06` | `remember` cache hit/miss |
| `T-07` | `add` فقط اگر نبود |
| `T-08` | Serialized object injection → رد |
| `T-09` | Redis نبود → fallback به file بدون crash |

## ۶. معیارهای پذیرش

`AC-CACHE-01`, `AC-CACHE-10`, `AC-CACHE-11`

## ۷. درایورها

`array`, `file`, `redis`, `memcached`, `apcu`, `database`

## ۸. جداول دیتابیس

- `cache`
- `cache_locks`

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