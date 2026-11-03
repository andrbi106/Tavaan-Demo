# نقشه راه تکمیل Tavaan-Engine-Session

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-041 |
| Engine ID | `tavaan.engine.session` |
| Namespace | `Tavaan\Engine\Session` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `http`, `crypt`, `cache` |
| Facade | `Session` |
| تخمین LOC | ۹۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/session.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/session.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 12 |
| کلاس‌ها | 12 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 1141 |

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
| `Session` | `get()`, `put()`, `has()`, `exists()`, `pull()`, `forget()`, `flush()`, `all()`, `push()`, `increment()`, `flash()`, `reflash()`, `keep()`, `regenerate()`, `migrate()`, `invalidate()`, `id()`, `token()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | start/get/put/has/flush |
| `T-02` | regenerate → ID جدید، داده حفظ |
| `T-03` | idle timeout → flush |
| `T-04` | fingerprint تغییر → invalidate |
| `T-05` | cookie HttpOnly/Secure/SameSite |
| `T-06` | flash فقط یک درخواست |
| `T-07` | Object injection در serialize → رد |
| `T-08` | ID نامعتبر → regenerate |
| `T-09` | همهٔ ۵ درایور |
| `T-10` | GC فایل‌های منقضی |

## ۶. معیارهای پذیرش

`AC-SES-01`, `AC-SES-11`

## ۷. درایورها

`array`, `file`, `cookie`, `database`, `redis`

## ۸. جداول دیتابیس

- `sessions`

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