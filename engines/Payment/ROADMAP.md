# نقشه راه تکمیل Tavaan-Engine-Payment

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-073 |
| Engine ID | `tavaan.engine.payment` |
| Namespace | `Tavaan\Engine\Payment` |
| اولویت | **P2** — فاز M7 |
| وابستگی‌ها | `http-client`, `database`, `event`, `crypt` |
| Facade | `Payment` |
| تخمین LOC | ۱۸۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/payment.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/payment.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 34 |
| کلاس‌ها | 34 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 3064 |

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
| `PaymentManager` | `purchase()`, `verify()`, `refund()`, `inquire()`, `gateway()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | purchase → redirect URL |
| `T-02` | verify success/failed/already |
| `T-03` | Callback با Idempotency (دو بار → یک اثر) |
| `T-04` | Amount tampering → رد (مبلغ محلی) |
| `T-05` | Callback جعلی بدون verify → رد |
| `T-06` | Refund |
| `T-07` | PAN mask |
| `T-08` | چند درگاه با یک Contract |
| `T-09` | Sandbox mode |

## ۶. معیارهای پذیرش

`AC-PAY-01`, `AC-PAY-10`

## ۸. جداول دیتابیس

- `tvn_payment_transactions`

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