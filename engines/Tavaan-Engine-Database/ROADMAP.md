# نقشه راه تکمیل Tavaan-Engine-Database

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-047 |
| Engine ID | `tavaan.engine.database` |
| Namespace | `Tavaan\Engine\Database` |
| اولویت | **P0** — فاز M3 |
| وابستگی‌ها | `config`, `event`, `log` |
| Facade | `DB` |
| تخمین LOC | ۳۲۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/database.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/database.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 82 |
| کلاس‌ها | 82 |
| فایل‌های تست | 3 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 11916 |

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
| `QueryBuilder` | `table()`, `select()`, `selectRaw()`, `from()`, `join()`, `leftJoin()`, `rightJoin()`, `crossJoin()`, `where()`, `orWhere()`, `whereIn()`, `whereNull()`, `whereBetween()`, `whereExists()`, `whereRaw()`, `groupBy()`, `having()`, `orderBy()`, `limit()`, `offset()`, `forUpdate()`, `sharedLock()`, `union()`, `withCte()`, `toSql()`, `getBindings()`, `get()`, `cursor()`, `first()`, `find()`, `value()`, `pluck()`, `count()`, `sum()`, `avg()`, `min()`, `max()`, `exists()`, `insert()`, `insertGetId()`, `insertOrIgnore()`, `upsert()`, `update()`, `increment()`, `decrement()`, `delete()`, `truncate()`, `paginate()`, `chunk()`, `lazy()` |
| `Connection` | `select()`, `selectOne()`, `cursor()`, `insert()`, `update()`, `delete()`, `statement()`, `affectingStatement()`, `lastInsertId()`, `transaction()`, `beginTransaction()`, `commit()`, `rollBack()`, `transactionLevel()`, `pdo()`, `queryLog()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | اتصال Lazy (PDO تا اولین کوئری ساخته نمی‌شود) |
| `T-02` | Reconnect پس از «gone away» |
| `T-03` | همهٔ Builder clauseها → SQL درست (MySQL/PG/SQLite) |
| `T-04` | Prepared binding با null/bool/int/string/DateTime |
| `T-05` | Nested transaction با Savepoint |
| `T-06` | Deadlock → retry (با Mock) |
| `T-07` | Chunk با توقف در callback |
| `T-08` | Cursor/Lazy حافظه ثابت نگه می‌دارد |
| `T-09` | Upsert در هر ۳ درایور |
| `T-10` | SQL Injection Fuzzing (۲۰۰ payload) → همه fail |
| `T-11` | `whereRaw` با binding کاربر → امن |
| `T-12` | Read/Write splitting + sticky |
| `T-13` | Query Log + Slow detection |
| `T-14` | N+1 detection |
| `T-15` | Pagination (Page/Cursor/Keyset) |

## ۶. معیارهای پذیرش

`AC-DB-01`, `AC-DB-02`, `AC-DB-03`, `AC-DB-04`, `AC-DB-05`

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