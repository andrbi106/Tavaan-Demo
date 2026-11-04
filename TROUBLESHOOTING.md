# مستندات رفع اشکال | Tavaan-Demo

> گزارش کامل خطاهای شناسایی‌شده هنگام راه‌اندازی پروژهٔ Tavaan-Demo و راه‌حل‌های اعمال‌شده.

## خلاصه

| ردیف | نوع خطا | تعداد فایل | علت ریشه‌ای |
|------|---------|-----------|-------------|
| ۱ | خطای نحوی (Parse) | ۲ | وجود `*/` در داخل کامنت بلوک `/* */` |
| ۲ | خطای نحوی (Parse) | ۱ | بک‌اسلشِ غیر‌escape‌شده در رشته |
| ۳ | خطای نحوی (Parse) | ۲۰ | `return return` تکراری + `;;` |
| ۴ | رابطِ PSR-11 مفقود | ۱ | وابستگی به `psr/container` بدون نصب Composer |
| ۵ | مسیر ریشه اشتباه | ۱ | `dirname(__DIR__)` به‌جای `__DIR__` در ریشه |
| ۶ | ارث‌بری از کلاس final | ۱ | `Container`.final، اما `Application` آن را extend می‌کرد |
| ۷ | سازندهٔ مفقود | ۱ | `Container` سازنده نداشت اما `Application` آن را صدا می‌زد |

**مجموع فایل‌های اصلاح‌شده:** ۲۶ + ۳ فایل جدید (stubهای PSR-11)
**نتیجه:** اجرای موفق CLI (`info`, `version`, `engines`, `config`, `serve`) و HTTP (`/`, `/api`, `/engines`, `/info`)

---

## محیط توسعه

چون PHP روی سیستم نصب نبود و دسترسی root هم وجود نداشت، از یک **باینری استاتیک PHP 8.3.32** (بدون نیاز به نصب) استفاده شد:

- منبع: `https://dl.static-php.dev/static-php-cli/common/php-8.3.32-cli-linux-x86_64.tar.gz`
- شامل ۳۸ افزونه شامل: mbstring, pdo, sqlite3, pdo_sqlite, curl, xml, dom, simplexml, json, tokenizer, openssl, ...
- مسیر نصب: `/home/z/my-project/tools/php`

این رویکرد با ادعای «صفر‌وابستگی Composer در زمان اجرا» در README هم‌خوانی دارد.

---

## خطای ۱ — `*/` در داخل کامنت بلوک (۲ فایل)

### فایل‌های درگیر
- `core/Autoloading/Autoloader.php` (خط ۱۰ و ۲۳)

### شرح
در کامنت‌های بلوک `/* ... */`، توضیحاتی مانند `Wildcard (engines/*/src/)` نوشته شده بود. در PHP، نخستین `*/` به‌عنوان پایان کامنت تفسیر می‌شود، بنابراین `src/)` به‌عنوان کد PHP باقی می‌ماند و خطای نحوی `Unmatched ')'` تولید می‌کرد.

### خطای گزارش‌شده
```
PHP Parse error:  Unmatched ')' in core/Autoloading/Autoloader.php on line 10
```

### راه‌حل
جایگزینی `*` با `<star>` در کامنت‌ها برای جلوگیری از تشکیل `*/`:

```diff
- *   - Wildcard (engines/*/src/)
+ *   - Wildcard (engines/<star>/src/)
```

```diff
- /** @var array<string, list<string>> wildcard prefixes (engines/*/src/) */
+ /** @var array<string, list<string>> wildcard prefixes (engines/<star>/src/) */
```

---

## خطای ۲ — بک‌اسلش غیرescape‌شده (۱ فایل)

### فایل درگیر
- `core/Support/PathGuard.php` (خط ۳۲)

### شرح
کد `$path = str_replace('\', '/', $path);` نوشته شده بود. در PHP، `\'` یک کوتیشنِ escape‌شده است، بنابراین رشته بسته نمی‌شود و باقی خط به‌عنوان کد نامعتبر تفسیر می‌شود.

### خطای گزارش‌شده
```
Parse error: syntax error, unexpected single-quoted string " || $part === ", expecting ")"
in core/Support/PathGuard.php on line 35
```

### راه‌حل
escape صحیح بک‌اسلش با دو بک‌اسلش:

```diff
- $path = str_replace('\', '/', $path);
+ $path = str_replace('\\', '/', $path);
```

---

## خطای ۳ — `return return` تکراری در فایل‌های زبان (۲۰ فایل)

### فایل‌های درگیر
```
engines/admin/lang/en/admin.php
engines/api/lang/en/api.php
engines/auth/lang/en/auth.php
engines/backup/lang/en/backup.php
engines/cookie/lang/en/cookie.php
engines/cookie/lang/fa/cookie.php
engines/feature-flag/lang/en/feature_flag.php
engines/migration/lang/en/migration.php
engines/payment/lang/en/payment.php
engines/plugin/lang/en/plugin.php
engines/queue/lang/en/queue.php
engines/rbac/lang/en/rbac.php
engines/realtime/lang/en/realtime.php
engines/report/lang/en/report.php
engines/router/lang/en/router.php
engines/scaffold/lang/en/scaffold.php
engines/schedule/lang/en/schedule.php
engines/security/lang/en/security.php
engines/session/lang/en/session.php
engines/workflow/lang/en/workflow.php
```

### شرح
همهٔ این فایل‌ها به‌جای `return [...];` به‌صورت `return return [...];;` نوشته شده بودند — کلیدواژهٔ `return` تکرار شده و دو سمیکالن انتهایی.

### خطای گزارش‌شده
```
Parse error: syntax error, unexpected token "return", expecting ";"
in engines/queue/lang/en/queue.php on line 2
```

### راه‌حل
حذف `return` تکراری و سمیکالن اضافی:

```diff
- <?php
- return return ['error_credentials' => 'Invalid credentials.'];;
+ <?php
+ return ['error_credentials' => 'Invalid credentials.'];
```

اصلاح به‌صورت خودکار با اسکریپت `preg_replace` روی همهٔ فایل‌های دارای الگو انجام شد.

---

## خطای ۴ — رابط PSR-11 مفقود (وابستگی پنهان به Composer)

### فایل درگیر
- `core/Contracts/Container/PsrContainerInterface.php`

### شرح
README ادعا می‌کند «صفر‌وابستگی Composer در زمان اجرا»، اما `PsrContainerInterface` رابط `Psr\Container\ContainerInterface` (از بستهٔ `psr/container`) را extend می‌کند. چون autoloader خودنوشته است و `vendor/` وجود ندارد، بارگذاری این رابط با خطا مواجه می‌شود.

`core/composer.json` نیز وابستگی `psr/container: ^2.0` را اعلام کرده، اما چون Composer نصب نیست، این وابستگی هرگز برآورده نمی‌شود.

### خطای گزارش‌شده
```
PHP Fatal error: Uncaught Error: Interface "Psr\Container\ContainerInterface" not found
in core/Contracts/Container/PsrContainerInterface.php:15
```

### راه‌حل
ایجاد **stub محلی PSR-11** در پروژه، با اولویتِ نسخهٔ رسمی Composer (در صورت نصب):

**فایل‌های جدید:**
- `core/Psr11/Container/ContainerInterface.php` (رابط PSR-11 اصلی: `get()` + `has()`)
- `core/Psr11/Container/ContainerExceptionInterface.php`
- `core/Psr11/Container/NotFoundExceptionInterface.php`

**اصلاح `config/autoload.php`:**
```php
// PSR-11 (stub محلی — فقط در صورت نبودِ نسخهٔ رسمی Composer)
// vendor/ در ابتدا قرار می‌گیرد تا اگر composer نصب باشد، اولویت با آن باشد.
'Psr\\Container\\'  => ['vendor/psr/container/src/', 'core/Psr11/Container/'],
```

این رویکرد هم سازگاری PSR-11 را حفظ می‌کند و هم ادعای «صفر‌وابستگی» را برآورده می‌سازد.

---

## خطای ۵ — مسیر ریشه اشتباه (`TVN_BASE_PATH`) — **ریشهٔ اصلی**

### فایل درگیر
- `bootstrap.php` (خط ۲۰)

### شرح
این **مهم‌ترین خطا** بود که باعث می‌شد autoloader نتواند هیچ فایلی را بیابد.

در ریشهٔ `bootstrap.php`:
```php
define('TVN_BASE_PATH', dirname(__DIR__));
```

چون `__DIR__` در این فایل برابر با `/home/z/.../Tavaan-Demo` است، `dirname(__DIR__)` برابر با `/home/z/.../workspace` (پدرِ پروژه) می‌شود — نه ریشهٔ پروژه!

در نتیجه تمام مسیرهای نسبی در `config/autoload.php` (مثل `core/`) به `/home/z/.../workspace/core/` حل می‌شدند که وجود نداشت.

**نکته:** `core/bootstrap.php` خط ۲۲ همین الگو را دارد اما آنجا `__DIR__` = `.../core` است، پس `dirname(__DIR__)` = `.../Tavaan-Demo` (درست). اما ریشهٔ `bootstrap.php` **اول** اجرا می‌شود و `TVN_BASE_PATH` را با مقدار اشتباه تعریف می‌کند، سپس `core/bootstrap.php` به‌خاطر `defined()` آن را بازنویسی نمی‌کند.

### خطای گزارش‌شده (مستقیم)
این خطا مستقیماً گزارش نمی‌شد، اما عامل پنهان تمام خطاهای «Class not found» بود.

### راه‌حل
استفاده از `__DIR__` به‌جای `dirname(__DIR__)`:

```diff
- if (!defined('TVN_BASE_PATH')) {
-     define('TVN_BASE_PATH', dirname(__DIR__));
- }
+ if (!defined('TVN_BASE_PATH')) {
+     define('TVN_BASE_PATH', __DIR__);
+ }
```

---

## خطای ۶ — ارث‌بری از کلاس `final`

### فایل‌های درگیر
- `core/Container/Container.php` (خط ۲۶: `final class Container`)
- `core/Application/Application.php` (خط ۱۳: `final class Application extends Container`)

### شرح
`Container` به‌صورت `final` اعلام شده بود، اما `Application` باید آن را extend کند (طبق طراحی: «Application خودش یک Container است»). PHP اجازهٔ extend از کلاس `final` را نمی‌دهد.

### خطای گزارش‌شده
```
PHP Fatal error:  Class Tavaan\Core\Application\Application cannot extend
final class Tavaan\Core\Container\Container in core/Application/Application.php on line 13
```

### راه‌حل
حذف `final` از اعلان `Container`:

```diff
- final class Container implements ContainerInterface
+ class Container implements ContainerInterface
```

---

## خطای ۷ — سازندهٔ مفقود در `Container`

### فایل‌های درگیر
- `core/Container/Container.php`
- `core/Application/Application.php` (خط ۲۳: `parent::__construct()`)

### شرح
بعد از رفع خطای ۶، خطای جدید ظاهر شد: `Application` در سازنده‌اش `parent::__construct()` را فراخوانی می‌کند، اما `Container` هیچ سازنده‌ای ندارد. در PHP 8+، فراخوانی `parent::__construct()` وقتی والد سازنده ندارد، خطای `Cannot call constructor` تولید می‌کند.

### خطای گزارش‌شده
```
PHP Fatal error:  Uncaught Error: Cannot call constructor
in core/Application/Application.php:23
```

### راه‌حل
افزودن سازندهٔ خالی به `Container`:

```php
/**
 * سازندهٔ Container.
 * خالی است زیرا propertyها با مقادیر پیش‌فرض مقداردهی شده‌اند، اما
 * لازم است تا زیرکلاس‌ها (مانند Application) بتوانند parent::__construct() را فراخوانی کنند.
 */
public function __construct()
{
    // no-op — initialization via property defaults
}
```

---

## بهبود فرعی — `.gitignore`

### شرح
فایل `config/engine.php` به‌صورت خودکار توسط `bootstrap.php` در هر اجرا بازنویسی می‌شود (merge پیکربندی موتورها). نسخهٔ tracked در مخزن فقط یک placeholder `return [];` بود. چون فایل خودکار تولید می‌شود، ردیابی آن در git باعث diffهای ناخواسته می‌شود.

همچنین فایل `.env` نیز باید gitignore شود (حاوی تنظیمات محیطی و به‌طور بالقوه اسرار).

### راه‌حل
```diff
+ # Auto-generated engine config (regenerated by bootstrap.php on every run)
+ config/engine.php
+
+ # Local environment
+ .env
```

و حذف `config/engine.php` از ردیابی git با `git rm --cached`.

---

## نحوهٔ اجرا پس از اصلاحات

```bash
# ۱) کلون مخزن
git clone https://github.com/andrbi106/Tavaan-Demo.git
cd Tavaan-Demo

# ۲) ایجاد فایل محیطی
cp .env.example .env

# ۳) اجرای سرور توسعه
php bin/tvn serve

# مرورگر: http://127.0.0.1:8000
```

### دستورات CLI
```bash
php bin/tvn info       # اطلاعات سیستم
php bin/tvn version    # نسخهٔ فریمورک
php bin/tvn engines    # لیست ۵۲ موتور
php bin/tvn config     # پیکربندی موتورها
```

### Endpointهای HTTP
| مسیر | توضیحات |
|------|---------|
| `GET /` | صفحهٔ خوش‌آمدگویی (HTML) |
| `GET /api` | وضعیت سیستم (JSON) |
| `GET /engines` | جدول موتورها (HTML) |
| `GET /info` | phpinfo() |

---

## توصیه‌های بعدی

۱. **آزمون واحد (Unit Tests):** پروژه موتور `test` دارد اما آزمونی اجرا نمی‌شود. توصیه می‌شود CI برای `php -l` روی همهٔ فایل‌ها و اجرای آزمون‌ها اضافه شود.

۲. **بررسی CI:** اکثر خطاهای بالا (به‌ویژه خطاهای نحوی و `return return`) با یک مرحلهٔ `php -l` در CI قابل شناسایی بودند.

۳. **حذف وابستگی پنهان:** یا `psr/container` به‌عنوان وابستگی Composer واقعی نصب شود، یا stub محلی (مانند این راه‌حل) به‌عنوان بخش رسمی پروژه مستند شود.

۴. **بازبینی `final`:** بررسی اینکه آیا کلاس‌های دیگر `final` هم هستند که فرزندی آن‌ها extend می‌کند.

۵. **باطل کردن توکن:** توکن GitHub ارائه‌شده در فایل آپلودی، عمومی شده است و باید فوراً در GitHub باطل شود (Settings → Developer settings → Personal access tokens → Revoke).
