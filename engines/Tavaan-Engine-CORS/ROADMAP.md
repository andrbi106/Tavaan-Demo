# نقشه راه تکمیل Tavaan-Engine-CORS

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-046 |
| Engine ID | `tavaan.engine.cors` |
| Namespace | `Tavaan\Engine\Http\Cors` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `http`, `config` |
| Facade | `—` |
| تخمین LOC | ۳۵۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/cors.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/cors.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 0 |
| کلاس‌ها | 0 |
| فایل‌های تست | 0 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 0 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 2 مورد (2 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `CorsMiddleware` (با 1 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_tests | مستندات 6 سناریوی تست (T-NN) تعریف می‌کند اما هیچ فایل تستی در `tests/` وجود ندارد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `CorsMiddleware` | `handle()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Simple request با origin مجاز → headers |
| `T-02` | origin غیرمجاز → بدون CORS header |
| `T-03` | Preflight OPTIONS → 204 با headers |
| `T-04` | wildcard `*.example.com` |
| `T-05` | `*` + credentials → config error |
| `T-06` | Vary: Origin حاضر |

## ۶. معیارهای پذیرش

`AC-CORS-01`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `CorsMiddleware` (با 1 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/CorsMiddleware.php` با namespace صحیح.
2. پیاده‌سازی متدهای: handle.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_tests

**شرح:** مستندات 6 سناریوی تست (T-NN) تعریف می‌کند اما هیچ فایل تستی در `tests/` وجود ندارد.

**گام‌ها:**
1. ایجاد `tests/Feature/<Engine>Test.php`.
2. نوشتن متد `test<Scenario>` برای هر T-NN در مستندات.
3. اجرای `php bin/tvn test` و رسیدن به پوشش کامل.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:34:08 UTC تولید شده است.*