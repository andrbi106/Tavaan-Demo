# نقشه راه تکمیل Tavaan-Engine-Router

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-040 |
| Engine ID | `tavaan.engine.router` |
| Namespace | `Tavaan\Engine\Http\Routing` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `http`, `container`, `config` |
| Facade | `Route` |
| تخمین LOC | ۱۴۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/router.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/router.md) |

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

**شکاف‌های شناسایی‌شده:** 3 مورد (3 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `Router` (با 25 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_class | کلاس `UrlGenerator` (با 11 متد عمومی) در پیاده‌سازی یافت نشد. |
| 3 | missing_tests | مستندات 18 سناریوی تست (T-NN) تعریف می‌کند اما هیچ فایل تستی در `tests/` وجود ندارد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Router` | `get()`, `post()`, `put()`, `patch()`, `delete()`, `options()`, `head()`, `match()`, `any()`, `view()`, `redirect()`, `permanentRedirect()`, `resource()`, `apiResource()`, `group()`, `prefix()`, `middleware()`, `domain()`, `fallback()`, `model()`, `bind()`, `match()`, `current()`, `currentRouteName()`, `has()` |
| `UrlGenerator` | `route()`, `action()`, `url()`, `asset()`, `current()`, `previous()`, `full()`, `signed()`, `temporarySignedRoute()`, `isValidSignature()`, `to()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | Static match با/بدون trailing slash |
| `T-02` | Param match با constraint `\d+` → `/abc` = 404 |
| `T-03` | اولویت static > param > wildcard |
| `T-04` | Method mismatch → 405 با `Allow` header |
| `T-05` | Group prefix + name + middleware merge |
| `T-06` | Domain routing با `{tenant}.example.com` |
| `T-07` | `apiResource` → ۵ روت درست |
| `T-08` | Nested resource + shallow |
| `T-09` | Model Binding با slug + Policy |
| `T-10` | `scopeBindings` → order متعلق به user دیگر = 404 |
| `T-11` | Attribute routing با group attribute |
| `T-12` | Route cache roundtrip → نتیجه یکسان |
| `T-13` | Collision detection در `route:check` |
| `T-14` | ReDoS regex → رد |
| `T-15` | UrlGenerator با پارامتر اضافه → query string |
| `T-16` | Signed URL با expires منقضی → invalid |
| `T-17` | Fallback route |
| `T-18` | ۱۰۰۰ روت → match ≤ ۵µs (benchmark) |

## ۶. معیارهای پذیرش

`AC-RT-01`, `AC-RT-07`, `AC-RT-08`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `Router` (با 25 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Router.php` با namespace صحیح.
2. پیاده‌سازی متدهای: get, post, put, patch, delete, options, head, match, any, view, redirect, permanentRedirect, resource, apiResource, group, prefix, middleware, domain, fallback, model, bind, match, current, currentRouteName, has.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_class

**شرح:** کلاس `UrlGenerator` (با 11 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/UrlGenerator.php` با namespace صحیح.
2. پیاده‌سازی متدهای: route, action, url, asset, current, previous, full, signed, temporarySignedRoute, isValidSignature, to.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-03 🔴 — missing_tests

**شرح:** مستندات 18 سناریوی تست (T-NN) تعریف می‌کند اما هیچ فایل تستی در `tests/` وجود ندارد.

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
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*