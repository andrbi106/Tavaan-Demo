# نقشه راه تکمیل Tavaan-Engine-Scaffold

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-077 |
| Engine ID | `tavaan.engine.scaffold` |
| Namespace | `Tavaan\Engine\Scaffold` |
| اولویت | **P1** — فاز M8 |
| وابستگی‌ها | `console`, `filesystem` |
| Facade | `—` |
| تخمین LOC | ۱۲۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/scaffold.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/scaffold.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 40 |
| کلاس‌ها | 40 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 0 |
| migrationها | 0 |
| LOC واقعی | 2075 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 1 مورد (1 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_config | هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `GeneratorManager` | `generate()`, `register()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Stub rendering (var/if/each) |
| `T-02` | make:model با همهٔ flagها |
| `T-03` | make:module ساختار کامل |
| `T-04` | overwrite با --force + backup |
| `T-05` | namespace صحیح برای module/app |
| `T-06` | نام نامعتبر → رد |

## ۶. معیارهای پذیرش

`AC-SCF-01`, `AC-SCF-02`, `AC-SCF-03`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_config

**شرح:** هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد).

**گام‌ها:**
1. ایجاد `config/<engine>.php` با همهٔ کلیدهای مستندشده.
2. افزودن به `config` در `module.json`.
3. تست با `php bin/tvn env:check`.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*