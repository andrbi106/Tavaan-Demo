# نقشه راه تکمیل Tavaan-Engine-Mail

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-065 |
| Engine ID | `tavaan.engine.mail` |
| Namespace | `Tavaan\Engine\Mail` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `view`, `queue`, `event`, `config` |
| Facade | `Mail` |
| تخمین LOC | ۱۴۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/mail.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/mail.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 18 |
| کلاس‌ها | 18 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 2266 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 4 مورد (0 بالا / 4 متوسط / 0 پایین).

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `cc()` در کلاس `Mailer` یافت نشد (پیاده‌سازی فعلی: 14 متد). |
| 2 | missing_method | متد `bcc()` در کلاس `Mailer` یافت نشد (پیاده‌سازی فعلی: 14 متد). |
| 3 | missing_method | متد `send()` در کلاس `Mailable` یافت نشد (پیاده‌سازی فعلی: 17 متد). |
| 4 | missing_method | متد `queue()` در کلاس `Mailable` یافت نشد (پیاده‌سازی فعلی: 17 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Mailer` | `to()`, `cc()`, `bcc()`, `send()`, `queue()`, `later()`, `raw()`, `fake()`, `assertSent()` |
| `Mailable` | `build()`, `subject()`, `view()`, `with()`, `attach()`, `replyTo()`, `send()`, `queue()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | MIME builder: multipart + attachment |
| `T-02` | Header encoding فارسی (RFC 2047) |
| `T-03` | SMTP با Mock server (array transport) |
| `T-04` | `Mail::fake()` + assertSent |
| `T-05` | Queue mail |
| `T-06` | Header injection → رد |
| `T-07` | STARTTLS handshake |

## ۶. معیارهای پذیرش

`AC-MAIL-01`, `AC-MAIL-08`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🟡 — missing_method

**شرح:** متد `cc()` در کلاس `Mailer` یافت نشد (پیاده‌سازی فعلی: 14 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `cc`.
3. افزودن تست Unit.

### TASK-02 🟡 — missing_method

**شرح:** متد `bcc()` در کلاس `Mailer` یافت نشد (پیاده‌سازی فعلی: 14 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `bcc`.
3. افزودن تست Unit.

### TASK-03 🟡 — missing_method

**شرح:** متد `send()` در کلاس `Mailable` یافت نشد (پیاده‌سازی فعلی: 17 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `send`.
3. افزودن تست Unit.

### TASK-04 🟡 — missing_method

**شرح:** متد `queue()` در کلاس `Mailable` یافت نشد (پیاده‌سازی فعلی: 17 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `queue`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*