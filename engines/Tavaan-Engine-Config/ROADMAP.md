# نقشه راه تکمیل Tavaan-Engine-Config

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-032 |
| Engine ID | `tavaan.engine.config` |
| Namespace | `Tavaan\Engine\Config` |
| اولویت | **P0** — فاز M1 |
| وابستگی‌ها | `container` |
| Facade | `Config` → `config` / `Env` → `env` |
| تخمین LOC | ۶۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/config.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/config.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 13 |
| کلاس‌ها | 13 |
| فایل‌های تست | 3 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1081 |

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
| `Repository` | `get()`, `set()`, `has()`, `missing()`, `forget()`, `pull()`, `push()`, `prepend()`, `all()`, `only()`, `except()`, `namespace()`, `freeze()`, `isFrozen()`, `toJson()`, `masked()`, `requiredKeys()` |
| `EnvRepository` | `get()`, `string()`, `bool()`, `int()`, `float()`, `list()`, `json()`, `required()`, `all()`, `has()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Dot-get در ۱/۲/۵ سطح عمق |
| `T-02` | `set` روی مسیر ناموجود → ساخت خودکار |
| `T-03` | Merge list = جایگزینی؛ map = ادغام |
| `T-04` | `.env` با quote، escape، inline comment، `true/false/null/empty` |
| `T-05` | کلید نامعتبر در `.env` → `EnvParseException` با شمارهٔ خط |
| `T-06` | Config Cache roundtrip یکسان |
| `T-07` | Closure در config → `ConfigNotCacheableException` |
| `T-08` | Frozen + `set()` → خطا |
| `T-09` | Schema: نوع اشتباه/`in` نامعتبر → خطا با مسیر کلید |
| `T-10` | Masking: `db.password` در `toJson()` → `********` |
| `T-11` | `Env::list` با جداکنندهٔ سفارشی |
| `T-12` | اولویت: module < app < env |

## ۶. معیارهای پذیرش

`AC-CFG-01`, `AC-CFG-02`, `AC-CFG-03`, `AC-CFG-04`, `AC-CFG-05`, `AC-CFG-06`

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