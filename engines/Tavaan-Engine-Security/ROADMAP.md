# نقشه راه تکمیل Tavaan-Engine-Security

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-059 |
| Engine ID | `tavaan.engine.security` |
| Namespace | `Tavaan\Engine\Security` |
| اولویت | **P0** — فاز M4 |
| وابستگی‌ها | `http`, `crypt`, `cache`, `session` |
| Facade | `Security` |
| تخمین LOC | ۱۵۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/security.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/security.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 11 |
| کلاس‌ها | 11 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 1436 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 1 مورد (1 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `Csrf` (با 4 متد عمومی) در پیاده‌سازی یافت نشد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Security` | `sanitize()`, `sign()`, `validateSignature()`, `normalize()`, `ssrfCheck()` |
| `RateLimiter` | `attempt()`, `tooManyAttempts()`, `hit()`, `remaining()`, `clear()`, `availableIn()` |
| `Csrf` | `token()`, `field()`, `rotate()`, `verify()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | CSRF: بدون توکن → 419 |
| `T-02` | CSRF: توکن درست → پاس |
| `T-03` | RateLimit: ۶۱ درخواست → 429 + Retry-After |
| `T-04` | Sliding window دقت |
| `T-05` | Sanitizer: `onerror=`, `javascript:` حذف |
| `T-06` | SSRF: `169.254.169.254` → رد |
| `T-07` | SSRF: DNS rebinding |
| `T-08` | Security headers همه حاضر |
| `T-09` | Signature با expires منقضی → رد |
| `T-10` | Normalizer: zero-width/bidi حذف |

## ۶. معیارهای پذیرش

`AC-SEC-01`, `AC-SEC-11`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `Csrf` (با 4 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Csrf.php` با namespace صحیح.
2. پیاده‌سازی متدهای: token, field, rotate, verify.
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