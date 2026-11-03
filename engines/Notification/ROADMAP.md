# نقشه راه تکمیل Tavaan-Engine-Notification

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-066 |
| Engine ID | `tavaan.engine.notification` |
| Namespace | `Tavaan\Engine\Notification` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `mail`, `queue`, `database`, `event` |
| Facade | `Notification` |
| تخمین LOC | ۹۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/notification.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/notification.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 33 |
| کلاس‌ها | 33 |
| فایل‌های تست | 5 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 2455 |

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
| `NotificationSender` | `send()`, `sendNow()`, `route()` |
| `Notification` | `via()`, `toMail()`, `toSms()`, `toDatabase()`, `toTelegram()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | send به چند کانال |
| `T-02` | `via()` انتخاب کانال |
| `T-03` | database channel + unread |
| `T-04` | Queue notification |
| `T-05` | SMS gateway با Mock |
| `T-06` | Rate limit |
| `T-07` | `Notification::fake()` + assertSentTo |

## ۶. معیارهای پذیرش

`AC-NOTIF-01`

## ۸. جداول دیتابیس

- `tvn_notifications`

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