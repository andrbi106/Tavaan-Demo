# نقشه راه تکمیل Tavaan-Engine-Crypt

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-054 |
| Engine ID | `tavaan.engine.crypt` |
| Namespace | `Tavaan\Engine\Crypt` |
| اولویت | **P0** — فاز M4 |
| وابستگی‌ها | `config` |
| Facade | `Crypt` |
| تخمین LOC | ۶۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/crypt.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/crypt.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 7 |
| کلاس‌ها | 7 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 415 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 3 مورد (3 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `Encrypter` (با 7 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_class | کلاس `Signer` (با 3 متد عمومی) در پیاده‌سازی یافت نشد. |
| 3 | missing_class | کلاس `BlindIndex` (با 2 متد عمومی) در پیاده‌سازی یافت نشد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Encrypter` | `encrypt()`, `decrypt()`, `encryptString()`, `encryptJson()`, `decryptJson()`, `hmac()`, `deriveKey()` |
| `Signer` | `sign()`, `verify()`, `generateKeyPair()` |
| `BlindIndex` | `compute()`, `verify()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | encrypt/decrypt roundtrip |
| `T-02` | tamper در ciphertext → `DecryptException` |
| `T-03` | IV منحصر برای دو encrypt یکسان |
| `T-04` | Rotation: decrypt با کلید قدیمی |
| `T-05` | Test Vectors استاندارد AES-GCM |
| `T-06` | HKDF در برابر RFC 5869 vectors |
| `T-07` | AAD mismatch → fail |
| `T-08` | BlindIndex deterministic |
| `T-09` | Ed25519 sign/verify |

## ۶. معیارهای پذیرش

`AC-CRYPT-01`, `AC-CRYPT-10`, `AC-CRYPT-11`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `Encrypter` (با 7 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Encrypter.php` با namespace صحیح.
2. پیاده‌سازی متدهای: encrypt, decrypt, encryptString, encryptJson, decryptJson, hmac, deriveKey.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_class

**شرح:** کلاس `Signer` (با 3 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Signer.php` با namespace صحیح.
2. پیاده‌سازی متدهای: sign, verify, generateKeyPair.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-03 🔴 — missing_class

**شرح:** کلاس `BlindIndex` (با 2 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/BlindIndex.php` با namespace صحیح.
2. پیاده‌سازی متدهای: compute, verify.
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
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:34:08 UTC تولید شده است.*