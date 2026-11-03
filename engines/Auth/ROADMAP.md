# نقشه راه تکمیل Tavaan-Engine-Auth

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-056 |
| Engine ID | `tavaan.engine.auth` |
| Namespace | `Tavaan\Engine\Auth` |
| اولویت | **P0** — فاز M4 |
| وابستگی‌ها | `session`, `hash`, `crypt`, `event`, `database` |
| Facade | `Auth` |
| تخمین LOC | ۲۰۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/auth.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/auth.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 25 |
| کلاس‌ها | 25 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 3072 |

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
| `AuthManager` | `guard()`, `user()`, `id()`, `check()`, `guest()`, `attempt()`, `once()`, `login()`, `loginUsingId()`, `logout()`, `validate()`, `token()`, `otp()`, `twoFactor()`, `devices()`, `register()`, `sendPasswordResetLink()`, `resetPassword()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | attempt با اعتبارنامهٔ درست/غلط |
| `T-02` | regenerate session در login (fixation) |
| `T-03` | Throttle: ۶ تلاش → ۴۲۹ + lockout |
| `T-04` | Remember me + recaller cookie |
| `T-05` | JWT encode/decode + `alg=none` رد |
| `T-06` | Algorithm confusion (RS256→HS256) رد |
| `T-07` | Refresh rotation + reuse detection |
| `T-08` | OTP یک‌بارمصرف + انقضا |
| `T-09` | TOTP با RFC 6238 vectors |
| `T-10` | rehash خودکار |
| `T-11` | پیام یکسان برای user/pass غلط |
| `T-12` | Device list + revoke |

## ۶. معیارهای پذیرش

`AC-AUTH-01`, `AC-AUTH-13`

## ۸. جداول دیتابیس

- `password_resets`
- `personal_access_tokens`
- `user_devices`
- `two_factor_secrets`

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