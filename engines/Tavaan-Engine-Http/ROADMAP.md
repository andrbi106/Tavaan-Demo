# نقشه راه تکمیل Tavaan-Engine-Http

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-039 |
| Engine ID | `tavaan.engine.http` |
| Namespace | `Tavaan\Engine\Http` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `pipeline`, `event`, `error`, `config`؛ نرم: `session`, `router`, `view` |
| Facade | `Http` → `http`، `Request`، `Response` |
| تخمین LOC | ۲۸۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/http.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/http.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 47 |
| کلاس‌ها | 47 |
| فایل‌های تست | 4 |
| فایل‌های پیکربندی | 1 |
| migrationها | 0 |
| LOC واقعی | 6351 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 3 مورد (0 بالا / 3 متوسط / 0 پایین).

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `handleSubRequest()` در کلاس `HttpKernel` یافت نشد (پیاده‌سازی فعلی: 5 متد). |
| 2 | missing_method | متد `redirectToRoute()` در کلاس `ResponseFactory` یافت نشد (پیاده‌سازی فعلی: 10 متد). |
| 3 | missing_method | متد `redirectToUrl()` در کلاس `ResponseFactory` یافت نشد (پیاده‌سازی فعلی: 10 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `HttpKernel` | `handle()`, `terminate()`, `sendAndTerminate()`, `handleSubRequest()` |
| `Request` | `capture()`, `create()`, `method()`, `path()`, `fullUrl()`, `header()`, `input()`, `query()`, `post()`, `json()`, `all()`, `only()`, `except()`, `file()`, `files()`, `cookie()`, `attribute()`, `ip()`, `isSecure()`, `expectsJson()`, `withAttribute()`, `withMethod()`, `withQuery()`, `withRoute()` |
| `Response` | `statusCode()`, `withStatus()`, `header()`, `withHeader()`, `withHeaders()`, `body()`, `withBody()`, `withJson()`, `cookie()`, `send()`, `isSuccessful()`, `isRedirect()` |
| `ResponseFactory` | `json()`, `view()`, `file()`, `download()`, `stream()`, `streamDownload()`, `noContent()`, `make()`, `redirectToRoute()`, `redirectToUrl()`, `problem()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | `Request::create()` با هر ۹ متد |
| `T-02` | Method override با `_method` و header |
| `T-03` | پارس JSON با عمق ۳۳ → خطا |
| `T-04` | `input()` اولویت: query < post < json (قابل تنظیم) |
| `T-05` | Immutable: `withAttribute` اصل را تغییر نمی‌دهد |
| `T-06` | `TrustProxies` با CIDR v4 و v6 |
| `T-07` | بدون trusted proxy → `X-Forwarded-For` نادیده |
| `T-08` | ETag + `If-None-Match` → 304 |
| `T-09` | `If-Modified-Since` → 304 |
| `T-10` | Compression فقط برای content-type فشرده‌شدنی و ≥ min_size |
| `T-11` | Header injection با `\r\n` → خطا |
| `T-12` | `Content-Length` + `Transfer-Encoding` → 400 |
| `T-13` | Upload با `UploadedFile` → `move()` به مسیر sandbox |
| `T-14` | `streamDownload` → chunked بدون مصرف حافظه |
| `T-15` | Terminate حتی با Exception اجرا می‌شود |
| `T-16` | Swoole adapter (با stub) → scope flush پس از هر درخواست |
| `T-17` | Response factory: همهٔ ۱۲ نوع |
| `T-18` | `Redirector::intended()` با session |

## ۶. معیارهای پذیرش

`AC-HTTP-01`, `AC-HTTP-02`, `AC-HTTP-03`, `AC-HTTP-04`, `AC-HTTP-05`, `AC-HTTP-06`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🟡 — missing_method

**شرح:** متد `handleSubRequest()` در کلاس `HttpKernel` یافت نشد (پیاده‌سازی فعلی: 5 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `handleSubRequest`.
3. افزودن تست Unit.

### TASK-02 🟡 — missing_method

**شرح:** متد `redirectToRoute()` در کلاس `ResponseFactory` یافت نشد (پیاده‌سازی فعلی: 10 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `redirectToRoute`.
3. افزودن تست Unit.

### TASK-03 🟡 — missing_method

**شرح:** متد `redirectToUrl()` در کلاس `ResponseFactory` یافت نشد (پیاده‌سازی فعلی: 10 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `redirectToUrl`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*