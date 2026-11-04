# کنوانسیون‌های نام‌گذاری تَوان

## ریشه‌های Namespace (قفل‌شده)

| Root | مسیر | محتوا |
|---|---|---|
| `Tavaan\Core\` | `core/` | هستهٔ صفر‌وابستگی |
| `Tavaan\Engine\<Name>\` | `engines/<kebab>/src/` | موتورهای خودنوشته |
| `Tavaan\Support\` | `core/Support/` | Helperها، Value Objectها |
| `Tavaan\Testing\` | `engines/test/src/` | ابزار تست |
| `App\` | `app/src/` | کد اپلیکیشن |
| `Modules\<Module>\` | `modules/<kebab>/src/` | ماژول‌های HMVC |
| `Kits\<Kit>\` | `kits/<kit>/src/` | کیت‌های اپلیکیشن |
| `Plugins\<Plugin>\` | `plugins/<plugin>/src/` | افزونه‌های ثالث (معاف از ZeroDependency) |

## قواعد نام‌گذاری

| موجودیت | قاعده | مثال |
|---|---|---|
| Namespace | PascalCase بخش‌ها | `Tavaan\Engine\Http\Routing` |
| کلاس/Interface/Trait/Enum | PascalCase | `RouteCompiler` |
| Interface | بدون پیشوند `I`؛ پسوند `*Interface` یا `*able` | `CacheStoreInterface`, `Cacheable` |
| متد | camelCase + فعل | `resolveRoute()` |
| Property/متغیر | camelCase | `$requestId` |
| ثابت/Enum case | UPPER_SNAKE | `MAX_RETRY`, `OrderStatus::Paid` |
| فایل غیرکلاسی | kebab-case | `route-cache.php` |
| کلید config | dot.snake.case (حداکثر ۴ سطح) | `app.cache.driver` |
| جدول DB | plural snake_case | `users`, `order_items` |
| ستون DB | snake_case | `discount_percent` |
| مسیر روت | kebab-case | `/blog/posts/{slug}` |
| رویداد (کلاس) | گذشته | `OrderWasPlaced` / `OrderPlaced` |
| Job | `<Verb><Noun>Job` | `SendOrderInvoiceJob` |
| تست | `test<Behavior>_<Condition>_<Expectation>` | `testPlaceOrder_WithEmptyCart_Throws` |

## پسوندهای کلاس (22 مورد)

`*Provider`, `*Engine`, `*Interface`, `*Manager`, `*Factory`, `*Builder`, `*Compiler`, `*Resolver`, `*Dispatcher`, `*Handler`, `*Middleware`, `*Command`, `*Job`, `*Listener`, `*Policy`, `*Request`, `*Resource`, `*Exception`, `*Test`, `*Driver`, `*Contract`, `*Adapter`, `*Guard`

## نام‌گذاری DB

| مورد | قاعده | مثال |
|---|---|---|
| نام DB | snake_case | `tavaan_shop` |
| جدول | plural snake_case | `users`, `order_items` |
| جدول محوری | `<singular>_<singular>` | `product_tag` |
| ستون PK | `id` (bigint unsigned یا `char(26)` ULID) | `id` |
| ستون FK | `<singular>_id` | `user_id` |
| Morph | `<name>_type` + `<name>_id` | `commentable_type`, `commentable_id` |
| Boolean | `is_*`, `has_*`, `can_*` | `is_active`, `has_paid` |
| تاریخ | `*_at` (datetime) / `*_date` (date) | `created_at`, `birth_date` |
| شمارنده | `*_count` | `view_count` |
| پول | `*_amount` + `*_currency` | `total_amount`, `currency` |
| ایندکس | `idx_<table>_<cols>` | `idx_orders_user_id` |
| یکتا | `uk_<table>_<cols>` | `uk_users_email` |
| FK | `fk_<table>_<ref>` | `fk_orders_user_id` |
| Migration | `YYYY_MM_DD_HHMMSS_<verb>_<table>.php` | `2026_11_04_120000_create_orders.php` |

## ستون‌های پایهٔ استاندارد (در هر جدول)

`id`, `created_at` (UTC datetime(3) NOT NULL), `updated_at` (NULL), `deleted_at` (NULL برای soft delete), `created_by`, `updated_by`, `tenant_id`, `version` (optimistic lock)

## نام‌های رزرو‌شده (ممنوع استفاده)

`tavaan, tvn, core, engine, engines, kernel, module, modules, kit, kits, plugin, plugins, app, admin, api, auth, cache, config, database, db, default, docs, events, http, log, queue, router, session, storage, system, temp, test, tests, tmp, vendor, views`

## شناسه‌ها در مستندات

| قالب | کاربرد |
|---|---|
| `FR-<AREA>-<NN>` | الزام عملکردی |
| `NFR-<AREA>-<NN>` | الزام غیرعملکردی |
| `AC-<ENGINE>-<NN>` | معیار پذیرش موتور |
| `SEC-<AREA>-<NN>` | کنترل امنیتی |
| `ADR-<NNNN>` | تصمیم معماری |
| `TVN-<NNNN>` | تیکت/issue |
| `DOC-<NNN>` | شناسه سند |
| `R<1-10>` | قانون سخت |
| `P<1-12>` | اصل بنیادین |

## پیشوندهای دیگر

| پیشوند | کاربرد |
|---|---|
| `TVN_` | متغیر ENV |
| `tvn:` | پیشوند کلید کش |
| `tvn:queue:` | پیشوند صف |
| `X-Tavaan-*` | هدر HTTP |
| `tvn_` (اختیاری) | پیشوند جدول DB |

## نام‌های فارسی↔لاتین کلیدی

| فارسی | لاتین |
|---|---|
| هسته | Kernel |
| موتور | Engine |
| ماژول | Module |
| کیت | Kit |
| افزونه | Plugin |
| قرارداد | Contract |
| ظرف | Container |
| ارائه‌دهنده | Provider |
| مرز | Boundary |
| رویداد | Event |
| شنونده | Listener |
| خط لوله | Pipeline |
| میان‌افزار | Middleware |
| کارخانه | Factory |
| سازنده | Builder |
| مدیر | Manager |
| انبار | Repository |
| درگاه | Gateway |
| موجودیت | Entity |
| شیء مقدار | Value Object |
| ریشهٔ تجمع | Aggregate Root |
| پایگاه داده | Database |
| جدول | Table |
| ستون | Column |
| کلید اصلی | Primary Key |
| کلید خارجی | Foreign Key |
| کوئری | Query |
| تراکنش | Transaction |
| مهاجرت | Migration |
| درخواست | Request |
| پاسخ | Response |
| مسیریاب | Router |
| کنترل‌کننده | Controller |
| نما | View |
| کوکی | Cookie |
| نشست | Session |
| احراز هویت | Authentication |
| کنترل دسترسی | Authorization |
| نقش | Role |
| مجوز | Permission |
| رمزنگاری | Encryption |
| درهم‌سازی | Hashing |
| صف | Queue |
| کار | Job |
| حافظهٔ نهان | Cache |
| گزارش | Log |
| رصدپذیری | Observability |
| صندوق خروجی | Outbox |
| صندوق ورودی | Inbox |
| کلید یکتایی | Idempotency Key |
| اجاره‌کننده | Tenant |
| چنداجاره‌ای | Multi-Tenancy |
| جلالی | Jalali |
| نیم‌فاصله | ZWNJ |
