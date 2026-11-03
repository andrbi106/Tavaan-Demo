# نقشه راه تکمیل Tavaan-Engine-Cookie

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-042 |
| Engine ID | `tavaan.engine.cookie` |
| Namespace | `Tavaan\Engine\Http\Cookie` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `http`, `crypt` |
| Facade | `Cookie` |
| تخمین LOC | ۴۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/cookie.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/cookie.md) |

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
| 1 | missing_class | کلاس `CookieJar` (با 7 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_tests | مستندات 6 سناریوی تست (T-NN) تعریف می‌کند اما هیچ فایل تستی در `tests/` وجود ندارد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `CookieJar` | `make()`, `forever()`, `queue()`, `get()`, `forget()`, `hasQueued()`, `queued()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | make/queue → Set-Cookie در پاسخ |
| `T-02` | encrypt/decrypt roundtrip |
| `T-03` | کوکی دستکاری‌شده → null |
| `T-04` | HttpOnly/Secure/SameSite پیش‌فرض |
| `T-05` | forget → expire گذشته |
| `T-06` | `except` برای کوکی‌های خاص |

## ۶. معیارهای پذیرش

`AC-COOK-01`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `CookieJar` (با 7 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/CookieJar.php` با namespace صحیح.
2. پیاده‌سازی متدهای: make, forever, queue, get, forget, hasQueued, queued.
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