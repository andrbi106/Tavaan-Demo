# قالب‌های بسط‌یافته برای کیت، ماژول و افزونه

> هر entity به‌جای یک فایل، یک **پوشه** با چند فایل ممیزی است.

---

## قالب کیت (۱۲ بخش + README)

مسیر: `docs/05-applications/<name>/`
شناسه: `DOC-09X` (X = ۱ تا ۸)

### بخش‌های اجباری

| شماره | فایل | عنوان | محتوا |
|---|---|---|---|
| 01 | `01-domain.md` | دامنه و مرزهای مسئله | Bounded Contexts، Aggregate Root، رویدادهای دامنه |
| 02 | `02-module-composition.md` | ترکیب ماژول‌ها | ماژول‌های اجباری/اختیاری، ماتریس وابستگی |
| 03 | `03-data-model.md` | مدل داده (SQL DDL) | جداول با ULID PK، ستون‌های پایه، decimal برای پول |
| 04 | `04-user-journeys.md` | مسیرهای کاربری | happy path، مسیرهای جایگزین/خطا، نمودار توالی |
| 05 | `05-roles-permissions.md` | نقش‌ها و مجوزها | نقش‌های سیستمی/تجاری، ماتریس نقش×مجوز، SoD |
| 06 | `06-events.md` | رویدادها | منتشرشده/مصرف‌شده، بین‌سرویسی، Outbox |
| 07 | `07-api.md` | API Endpoints | REST با /api/v1، نسخه‌گذاری، صفحه‌بندی cursor |
| 08 | `08-advanced-features.md` | قابلیت‌های پیشرفته | جستجوی فارسی، کوپن، مالیات، چندانباری |
| 09 | `09-performance.md` | یادداشت‌های کارایی | cursor pagination، FOR UPDATE، CDN، CQRS |
| 10 | `10-e2e-tests.md` | سناریوهای E2E | حداقل ۵ سناریو با ID (E2E-NN) |
| 11 | `11-acceptance.md` | معیار پذیرش 🧪 | AC-<KIT>-NN با تست، نصب با `tvn kit:install` |
| 12 | `12-machine-readable.md` | بلوک ماشین‌خوان 🧠 | YAML با id, version, architecture, modules[] |

### کیت‌های موجود

| DOC-ID | کیت | عنوان | معماری |
|---|---|---|---|
| DOC-091 | Blog | وبلاگ | mvc |
| DOC-092 | News-Portal | خبرگزاری | mvc |
| DOC-093 | Forum | انجمن | hmvc |
| DOC-094 | Shop | فروشگاه | modular |
| DOC-095 | LMS | مدیریت یادگیری | modular |
| DOC-096 | CRM | مدیریت ارتباط با مشتری | hexagonal |
| DOC-097 | ERP | برنامه‌ریزی منابع | hexagonal |
| DOC-098 | BPMS | مدیریت فرآیندها | hexagonal |

---

## قالب ماژول (۱۵ بخش + README)

مسیر: `docs/10-modules/<name>/`
شناسه: `DOC-16X` (X = ۱ تا ۶)

### بخش‌های اجباری

| شماره | فایل | عنوان | محتوا |
|---|---|---|---|
| 01 | `01-goal-scope.md` | هدف و دامنه | Why/What/الزامات |
| 02 | `02-dependencies.md` | وابستگی‌ها | موتورهای اجباری، ماژول‌های اجباری/اختیاری |
| 03 | `03-structure.md` | ساختار پوشه | src/ با Contracts, Domain, Http؛ config/، lang/، migrations/ |
| 04 | `04-public-api.md` | API عمومی | Facade با @mixin، helper functions، ServiceProvider |
| 05 | `05-config.md` | پیکربندی | config/<name>.php، جدول کلید/نوع/پیش‌فرض/ENV |
| 06 | `06-contracts.md` | قراردادها | Interface در core/Contracts/، پیاده‌سازی، Fake |
| 07 | `07-events.md` | رویدادها | منتشرشده/مصرف‌شده، Hook/Extend |
| 08 | `08-database.md` | جداول پایگاه داده | DDL، ستون‌های پایه، ایندکس، migration |
| 09 | `09-permissions.md` | مجوزها | کلید استاندارد، نقش‌های پیش‌فرض، Gate/Policy |
| 10 | `10-routes.md` | مسیرها | web.php، api.php با /api/v1، middleware گروهی |
| 11 | `11-views.md` | نماها | قالب‌های .tv.php، Layout/Component، RTL |
| 12 | `12-tests.md` | تست | Unit ≥۸۰٪، Feature، Integration، E2E |
| 13 | `13-example.md` | مثال End-to-End | نمونهٔ اجراشدنی PHP با declare(strict_types=1) |
| 14 | `14-acceptance.md` | معیار پذیرش 🧪 | AC-<MODULE>-NN، نصب با `tvn module:install` |
| 15 | `15-machine-readable.md` | بلوک ماشین‌خوان 🧠 | YAML با id, version, providers, requires |

### ماژول‌های موجود

| DOC-ID | ماژول | عنوان | اولویت |
|---|---|---|---|
| DOC-161 | core-user | هستهٔ کاربر | P0 |
| DOC-162 | content | محتوا | P0 |
| DOC-163 | comment | نظر | P1 |
| DOC-164 | category | دسته‌بندی | P1 |
| DOC-165 | shop | فروشگاه | P0 |
| DOC-166 | inventory | انبار | P0 |
| DOC-167 | payment | پرداخت دامنه | P0 |
| DOC-168 | shipping | ارسال | P1 |
| DOC-169 | tax | مالیات | P1 |
| DOC-170 | accounting | حسابداری | P0 |
| DOC-171 | procurement | خرید | P1 |
| DOC-172 | sales | فروش | P0 |
| DOC-173 | hr | منابع انسانی | P1 |
| DOC-174 | production | تولید | P1 |
| DOC-175 | asset | دارایی | P1 |
| DOC-176 | form-builder | سازنده فرم | P1 |

---

## قالب افزونه (۱۴ بخش + README)

مسیر: `docs/11-plugins/<name>/`
شناسه: `DOC-17X` (X = ۱ تا ۴)

### بخش‌های اجباری

| شماره | فایل | عنوان | محتوا |
|---|---|---|---|
| 01 | `01-goal-scope.md` | هدف و دامنه | نوع افزونه (feature/theme/payment) |
| 02 | `02-manifest.md` | فایل manifest | plugin.json با id, name, version, autoload.namespace-map |
| 03 | `03-namespace-autoload.md` | فضای‌نام و Autoload | Plugins\Vendor\Name، ثبت روی Autoloader هسته |
| 04 | `04-provider.md` | Service Provider | register() بدون side-effect، boot() با Hook |
| 05 | `05-permissions-capabilities.md` | مجوزها و قابلیت‌ها | permissions، capabilities (network/fs/db/shell) |
| 06 | `06-hooks.md` | Hookها | ثبت‌شده، اولویت، payload، flush در disable |
| 07 | `07-signature.md` | امنیت و امضا | signature با کلید عمومی، verify در install |
| 08 | `08-config.md` | پیکربندی | config/<name>.php، publish با `tvn plugin:publish` |
| 09 | `09-database.md` | پایگاه داده | migrations، جدول‌ها با پیشوند، seed_on_install |
| 10 | `10-events.md` | رویدادها | منتشرشده/مصرف‌شده، integration-event schema |
| 11 | `11-installation.md` | نصب | `tvn plugin:install`، steps، post_install، health_check |
| 12 | `12-tests.md` | تست | Unit، Security، Sandbox isolation |
| 13 | `13-acceptance.md` | معیار پذیرش 🧪 | AC-<PLUGIN>-NN، نصب بدون خطا، vendor ایزوله |
| 14 | `14-machine-readable.md` | بلوک ماشین‌خوان 🧠 | YAML با id, version, vendor, capabilities, hooks |

### افزونه‌های نمونه

| DOC-ID | افزونه | نوع | فروشنده |
|---|---|---|---|
| DOC-171 | example-analytics | feature | tavaan.example |
| DOC-172 | example-payment-gateway | payment | tavaan.example |
| DOC-173 | example-theme | theme | tavaan.example |
| DOC-174 | example-seo | feature | tavaan.example |

### قواعد افزونه‌ها 🔒

۱. هر افزونه باید `plugin.json` معتبر داشته باشد
۲. امضای دیجیتال الزامی است (در صورت فعال بودن)
۳. Sandbox ایزوله برای بارگذاری
۴. vendor باید در `allowed_plugin_vendors` باشد
۵. قابلیت‌ها در زمان اجرا enforce می‌شوند
۶. افزونه‌ها از قاعدهٔ ZeroDependency **معاف** هستند (می‌توانند vendor داشته باشند)

---

## ساختار هر فایل ممیزی عملیاتی

هر فایل دارای:

```markdown
# DOC-XXX-SNN — <عنوان بخش> (<نوع> <slug>)

| متادیتا | مقدار |
|---|---|
| DOC-ID | DOC-XXX-SNN |
| موتور/کیت/ماژول/افزونه | `<slug>` |
| بخش | NN — <عنوان> |
| وضعیت | Draft v0.0.1 |
| نوع | فایل ممیزی عملیاتی |

---

## شرح بخش (یا محتوای فعلی)
<محتوا یا توضیح>

---

## چک‌لیست ممیزی عملیاتی
- [ ] آیتم ۱
- [ ] آیتم ۲
- [ ] آیتم ۳

---

## وضعیت ممیزی
| شاخص | وضعیت |
|---|---|
| محتوا تکمیل شده؟ | ✅/⚠️/❌ |
| تعداد آیتم‌های چک‌لیست | N |
| نیاز به تکمیل | بله/خیر |

---

## یادداشت‌های ممیزی‌کننده
- **تاریخ ممیزی:** _TODO_
- **ممیزی‌کننده:** _TODO_
- **یادداشت:** _TODO_
```
