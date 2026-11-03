# نقشه راه تکمیل Tavaan-Engine-Search

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-052 |
| Engine ID | `tavaan.engine.search` |
| Namespace | `Tavaan\Engine\Search` |
| اولویت | **P1** — فاز M6 |
| وابستگی‌ها | `database`, `event` |
| Facade | `Search` |
| تخمین LOC | ۱۵۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/search.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/search.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 15 |
| کلاس‌ها | 15 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 4002 |

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
| `SearchManager` | `index()`, `delete()`, `indexMany()`, `search()`, `suggest()`, `createIndex()`, `dropIndex()`, `flush()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Native index + search فارسی |
| `T-02` | نرمال‌سازی ی/ک/نیم‌فاصله |
| `T-03` | Stemming |
| `T-04` | Facet counting |
| `T-05` | Highlight (بدون XSS) |
| `T-06` | MySQL/PG/SQLite FTS |
| `T-07` | ایندکس خودکار از رویداد |
| `T-08` | BM25 ranking |

## ۶. معیارهای پذیرش

`AC-SEARCH-01`, `AC-SEARCH-09`

## ۷. درایورها

`mysql`, `pgsql`, `sqlite`, `native`, `meilisearch`, `typesense`

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