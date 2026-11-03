# نقشه راه تکمیل Tavaan-Engine-Http-Client

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-064 |
| Engine ID | `tavaan.engine.http-client` |
| Namespace | `Tavaan\Engine\HttpClient` |
| اولویت | **P1** — فاز M5 |
| وابستگی‌ها | `event`, `cache`, `config`؛ نرم: `security` (SSRF) |
| Facade | `Http` |
| تخمین LOC | ۱۳۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/http-client.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/http-client.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 19 |
| کلاس‌ها | 19 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 2448 |

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
| `Factory` | `get()`, `post()`, `put()`, `patch()`, `delete()`, `withHeaders()`, `withToken()`, `asJson()`, `asForm()`, `timeout()`, `retry()`, `pool()`, `fake()`, `assertSent()`, `withQueryParameters()`, `attach()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | get/post/put/delete با fake |
| `T-02` | `Http::fake()` + assertSent |
| `T-03` | Retry روی timeout |
| `T-04` | Circuit breaker: open پس از ۵ خطا |
| `T-05` | SSRF: block IP خصوصی |
| `T-06` | Redirect با SSRF re-check |
| `T-07` | Pool موازی |
| `T-08` | JSON/form/multipart |
| `T-09` | curl نبود → stream fallback |

## ۶. معیارهای پذیرش

`AC-HTTPC-01`

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