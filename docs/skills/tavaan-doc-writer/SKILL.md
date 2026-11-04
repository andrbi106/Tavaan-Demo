---
name: tavaan-doc-writer
description: >-
  Skill اختصاصی برای ممیزی، مقایسه و بازنویسی مستندات پروژه‌های مبتنی بر فریمورک تَوان (Tavaan).
  این skill ساختار مستندات مرجع (10 بخش شماره‌گذاری‌شده، ADRها، قالب‌ها، schemaها)،
  کنوانسیون‌ها (DOC-ID، جدول متادیتا، نمادهای 🔒⚠️💡🧪🧠، RFC 2119)، قوانین سخت (R1-R10)،
  اصول بنیادین (P1-P12)، و قالب‌های ۱۷بخشی موتور و ۷بخشی ADR را در اختیار می‌گذارد.
  از این skill برای نوشتن مستندات جدید تَوان، ممیزی انطباق پروژه با اصول ZeroDependency،
  شناسایی تفاوت‌ها و تناقضات بین کد و مستندات، و تولید manifest ماشین‌خوان استفاده کنید.
  این skill را هر زمان که کاربر از تَوان، Tavaan، مستندات فریمورک فارسی، ZeroDependency،
  ممیزی پروژه PHP، یا ساخت مستندات موتور/ADR/ماژون می‌گوید، فعال کنید.
---

# Tavaan Doc Writer — Skill ممیزی و مستندنویسی تَوان

## نقش این skill

این skill حاصلِ مطالعهٔ کامل (فایل به فایل، خط به خط) مخزن مرجع `Derogaramin/Tavaan-Doc` است.
هدف: توانایی بازتولید مستندات تَوان با حفظ کنوانسیون‌ها، و ممیزی پروژه‌های تَوان برای انطباق.

## هویت تَوان

| ویژگی | مقدار |
|---|---|
| نام فارسی | تَوان |
| نام لاتین | Tavaan |
| شعار | صفر وابستگی، بی‌نهایت امکان / Zero Dependency. Infinite Capability. |
| نسخهٔ مستند | 1.0.0 |
| حداقل PHP | 8.2.0 |
| سیاست وابستگی | zero-external (هیچ پکیج بیرونی در core/ و engines/) |
| معماری‌ها | layered, hmvc, modular-monolith, event-driven, hexagonal |

## ساختار مستندات مرجع (10 بخش + ADR + قالب + schema)

```
docs/
├── README.md                  DOC-000 — شاخص ناوبری + جدول کامل DOC-ID
├── manifest.json              🧠 فهرست ماشین‌خوان (121 سند + 53 موتور)
├── 00-overview/               اهداف، اصول، الزامات، شروع سریع
├── 01-standards/              PHP 8.2، سبک کد، نام‌گذاری، خطا، قراردادها، مستندسازی، امنیت
├── 02-architecture/           9 سند: overview, layered-hmvc, modular-monolith, event-driven,
│                              microservice-ready, request-lifecycle, container-di, bootstrapping, multi-tenancy
├── 03-engines/                README (رجیستری) + 53 سند موتور (هر کدام 17 بخش)
├── 04-structure/              folder-structure, file-conventions, composer-mapping, cli-reference,
│                              config-reference, database-schema-catalog
├── 05-applications/           README + 8 بلوپرینت (blog, shop, forum, lms, news-portal, erp, bpms, crm)
├── 06-roles-access/           roles-matrix, permission-catalog, duties-workflow
├── 07-quality/                testing-strategy, static-analysis-ci, benchmark-targets, definition-of-done
├── 08-roadmap/                milestones (M0-M9), agent-backlog (63 تیکت YAML)
├── 09-references/             benchmarks, glossary (واژه‌نامه فارسی↔لاتین)
├── adr/                       README + 16 ADR (ADR-0001 تا ADR-0016)
├── schemas/                   4 JSON Schema: module.v1, project.v1, integration-event.v1, docs-manifest.v1
└── templates/                 engine-spec-template, module-template, adr-template
tools/scripts/                 verify-docs.py, build-manifest.py, scaffold-structure.sh
```

## کنوانسیون‌های اجباری

### ۱) شناسهٔ DOC-ID
هر سند دارای شناسهٔ یکتا است:
- `DOC-000` — شاخص
- `DOC-001..004` — overview
- `DOC-010..016` — standards
- `DOC-020..028` — architecture
- `DOC-030` — رجیستری موتورها
- `DOC-031..075` — مستند هر موتور (هر موتور یک DOC-ID)
- `DOC-080..086` — structure
- `DOC-090..098` — applications
- `DOC-100..102` — roles-access
- `DOC-110..113` — quality
- `DOC-120..121` — roadmap
- `DOC-130..131` — references
- `DOC-140` — شاخص ADR
- `DOC-150..152` — قالب‌ها
- `ADR-NNNN` — تصمیمات معماری (ترتیبی، غیرقابل تغییر)

### ۲) جدول متادیتا (بالای هر سند)
```markdown
# DOC-0XX — <عنوان فارسی>

| متادیتا | مقدار |
|---|---|
| DOC-ID | DOC-0XX |
| وضعیت | Draft / Review / Approved v1.0 / Proposed |
| نقش | <نقش یک‌خطی> |
| پیش‌نیاز | DOC-0YY (اختیاری) |
| مرتبط | DOC-0YY, ADR-NNNN (اختیاری) |
```

برای مستند موتور، جدول غنی‌تر است (Engine ID, Namespace, مسیر, اولویت P0/P1/P2, فاز M1..M8, وابستگی سخت/نرم, افزونه‌های PHP, Facade, جداول DB, LOC برآوردی, مسئول).

### ۳) نمادها (RFC 2119)
| نماد | معنا | معادل RFC 2119 |
|---|---|---|
| 🔒 | الزامی — نقض = ریجکت کد | MUST / MUST NOT |
| ⚠️ | توصیهٔ قوی | SHOULD |
| 💡 | اختیاری/بهتر است | MAY |
| 🧪 | دارای معیار تست/پذیرش | — |
| 🧠 | دارای بلوک ماشین‌خوان (YAML) | — |

### ۴) دوزبانگی
- **نثر فارسی** برای توضیحات (RTL)
- **شناسه‌های لاتین** برای: namespace، کلاس، متد، فایل، کلید config، ENV، CLI، مسیر
- اولین occurrence: `واژهٔ فارسی (Latin)`، سپس فقط فارسی
- ارقام: فارسی در نثر، لاتین در کد/شناسه

### ۵) شماره‌گذاری بخش‌ها
- اعداد فارسی: `۱.`, `۲.`, `۳.`
- زیربخش‌ها با جداکنندهٔ `٫` (U+066B): `۱٫۱`, `۱٫۲`

## ۱۰ قانون سخت (R1-R10) — دروازهٔ ریجکت خودکار

```
R1  هر فایل PHP با declare(strict_types=1); شروع می‌شود.
R2  هیچ require/include دستی در کد کاربردی؛ فقط Autoloader هسته.
R3  هیچ پکیج خارجی در core/ و engines/ ممنوع است (ZeroDependency).
R4  هیچ Property پویا؛ کلاس‌ها یا readonly یا typed و تعریف‌شده.
R5  هر خروجی عمومی باید Interface داشته باشد؛ پیاده‌سازی پشت قرارداد.
R6  هر موتور: Provider + Interface + Tests + Docs + Benchmark. هیچ‌کدام اختیاری نیست.
R7  نام namespace فقط از ریشهٔ ثبت‌شده.
R8  خطاها استثنا هستند با کد عددی و MessageId ترجمه‌پذیر.
R9  هیچ کوئری با الحاق رشته؛ فقط Prepared Statement / Query Builder.
R10 قبل از Merge: php -l + PHPStan max + تست‌ها سبز + Benchmark بدون افت.
```

## ۱۲ اصل بنیادین (P1-P12)

| # | اصل | خلاصه |
|---|---|---|
| P1 | Zero Dependency | هیچ پکیج بیرونی در core/engines؛ افزونه PHP با fallback مجاز |
| P2 | Contract-First | Interface قبل از پیاده‌سازی |
| P3 | Minimal Kernel | هسته = حداقل؛ هرچه قابل‌حذف است → در موتور |
| P4 | Explicit Boundaries | مرز ماژول؛ دسترسی فقط از درگاه عمومی |
| P5 | Events over Calls | بین دامنه‌ها با رویداد، نه فراخوانی مستقیم |
| P6 | Fail Fast & Loud | بدون catch خالی یا @ suppression |
| P7 | Secure by Default | auto-escape، prepared statement، CSRF هم‌روشن |
| P8 | No Hidden Magic | Facade = Lazy Proxy + @mixin؛ بدون property پویا |
| P9 | SRP + Small Surface | کلاس ≤300 خط، متد ≤50 خط، ≤5 پارامتر |
| P10 | Readable Performance | بهینه‌سازی فقط با اندازه‌گیری |
| P11 | Language Forward-Compat | کد روی 8.2، اجرای بدون Deprecated تا 8.4/9 |
| P12 | Extensibility Triad | هر موتور 3 نقطهٔ توسعه: Hook + Contract + Decorator |

**ترتیب قطع‌کننده (تعارض اصول):** Correctness > Security > Simplicity > Performance > Flexibility > Convenience

## قالب مستند موتور (۱۷ بخش اجباری)

برای ساخت مستند هر موتور، قالب کامل در `references/engine-template.md` را کپی کنید.
هیچ بخشی نباید حذف شود مگر با `N/A: <دلیل>`.

خلاصهٔ ۱۷ بخش:
1. هدف و دامنه (Why/What/الزامات/مقایسه)
2. مفاهیم و واژگان
3. معماری داخلی (نمودار اجزا، فهرست کلاس، توالی، وضعیت)
4. API عمومی (امضاها، Facade، helper، attribute)
5. الگوریتم‌ها و ساختار داده (Big-O، حالت‌های مرزی)
6. پیکربندی (config + جدول کلیدها)
7. قراردادها (Interface + پیاده‌سازی‌ها)
8. نقاط توسعه (رویدادها، hook، درایور سفارشی)
9. امنیت (تهدید/کنترل + چک‌لیست)
10. کارایی (اهداف + روش سنجش)
11. مدیریت خطا (جدول استثنا/کد/MessageId)
12. تست (استراتژی + سناریو + fixture)
13. مثال End-to-End
14. معیار پذیرش 🧪 (AC-<NAME>-NN)
15. محدودیت‌ها و کارهای آینده
16. مراجع
17. بلوک ماشین‌خوان 🧠 (YAML)

## قالب ADR (۷ بخش)

```
# ADR-NNNN — <عنوان>
| متادیتا | مقدار |
## ۱. زمینه (Context)
## ۲. تصمیم (Decision)
## ۳. گزینه‌های بررسی‌شده (Options) — جدول A/B/C-selected
## ۴. دلایل انتخاب (Rationale) — ارجاع به اصل
## ۵. پیامدها (Consequences) — مثبت/منفی/ریسک
## ۶. انطباق (Compliance)
## ۷. اعتبارسنجی (Validation) 🧪
```

قواعد ADR: شماره ترتیبی هرگز تغییر نمی‌کند؛ ADR پذیرفته‌شده تغییر نمی‌کند (اصلاح با ADR جدید)؛ هر ADR باید به اصل ارجاع دهد.

## ۱۶ ADR مرجع

| شماره | عنوان | وضعیت |
|---|---|---|
| ADR-0001 | معماری ترکیبی (HMVC + Modular + ED + Hexagonal) | Accepted |
| ADR-0002 | مدل مجوزدهی (MIT هسته / تجاری Enterprise) | Proposed |
| ADR-0003 | حداقل نسخهٔ PHP = 8.2 | Accepted |
| ADR-0004 | صفر وابستگی در هسته | Accepted |
| ADR-0005 | Autoloader خودنوشته با ClassMap Cache | Accepted |
| ADR-0006 | شناسهٔ اصلی: ULID | Accepted |
| ADR-0007 | ORM دوگانه (Active Record + Repository) | Accepted |
| ADR-0008 | پسوند فایل View: `.tv.php` | Accepted |
| ADR-0009 | Outbox به‌عنوان الگوی پیش‌فرض | Accepted |
| ADR-0010 | Multi-Tenancy با Strategy Pattern | Accepted |
| ADR-0011 | Context با Fiber-Aware Storage | Accepted |
| ADR-0012 | تقویم جلالی در موتور i18n | Accepted |
| ADR-0013 | پول با int minor-unit + Money VO | Accepted |
| ADR-0014 | صفر vendor در زمان اجرا | Accepted |
| ADR-0015 | نسخه‌گذاری API با /api/v1 + Header | Accepted |
| ADR-0016 | Lazy Proxy بدون eval | Accepted |

## فرآیند ممیزی پروژه (Audit Workflow)

وقتی کاربر می‌گوید «پروژه را بررسی کن» یا «مقایسه کن»:

### مرحلهٔ ۱: بررسی قوانین سخت (R1-R10)
برای هر قانون، فایل‌های PHP را اسکن کن و نقض‌ها را فهرست کن:
- R1: `grep -rL "declare(strict_types=1)" --include="*.php"` → فایل‌های بدون strict_types
- R3: جستجوی `composer.json` و `use Psr\` و `require.*vendor` → نقض ZeroDependency
- R4: جستجوی dynamic property (PHP 8.2 deprecation)
- R5: کلاس‌های public بدون Interface
- R8: `throw new \Exception(` بدون کلاس اختصاصی / بدون MessageId
- R9: کوئری با الحاق رشته (`"...$var..."` در SQL)

### مرحلهٔ ۲: بررسی کنوانسیون نام‌گذاری
- Namespace root فقط از ریشه‌های مجاز
- نام کلاس PascalCase
- نام متد camelCase + فعل
- نام جدول plural snake_case
- مسیر روت kebab-case

### مرحلهٔ ۳: بررسی ساختار پوشه
مقایسه با `references/folder-structure.md`.

### مرحلهٔ ۴: بررسی مستندات
- هر موتور دارای مستند ۱۷بخشی؟
- DOC-ID یکتا؟
- بلوک ماشین‌خوان YAML موجود؟
- لینک‌های داخلی معتبر؟

### مرحلهٔ ۵: تولید گزارش
قالب گزارش ممیزی در `references/audit-report-template.md`.

## فرآیند تولید مستندات (Doc Generation Workflow)

وقتی کاربر می‌گوید «مستندات بنویس»:

1. **تعیین نوع سند**: overview / standard / architecture / engine / structure / application / roles / quality / roadmap / reference / ADR
2. **کپی قالب مربوط** از `references/`
3. **تخصیص DOC-ID** بعدی (در صورت نبود manifest، از DOC-001 شروع کن)
4. **پر کردن جدول متادیتا**
5. **نوشتن بخش‌ها** به ترتیب، با رعایت دوزبانگی و نمادها
6. **افزودن بلوک ماشین‌خوان YAML** در انتها (در صورت موتور/استاندارد)
7. **به‌روزرسانی manifest.json** (اگر موجود)
8. **اجرای verify-docs.py** برای اعتبارسنجی

## فایل‌های مرجع

برای جزئیات کامل، فایل‌های مرجع زیر را بخوان:

| فایل | محتوا | چه زمانی بخوان |
|---|---|---|
| `references/engine-template.md` | قالب کامل ۱۷بخشی موتور | وقتی مستند موتور می‌نویسی |
| `references/adr-template.md` | قالب ۷بخشی ADR + فهرست 16 ADR | وقتی ADR می‌نویسی |
| `references/conventions.md` | کنوانسیون‌های نام‌گذاری + جدول کامل | وقتی نام‌گذاری می‌کنی |
| `references/folder-structure.md` | ساختار کامل پوشه‌ها | وقتی ساختار بررسی/ایجاد می‌کنی |
| `references/audit-checklist.md` | چک‌لیست ممیزی R1-R10 + P1-P12 | وقتی پروژه ممیزی می‌کنی |
| `references/glossary.md` | واژه‌نامه فارسی↔لاتین | وقتی واژه‌ای را ترجمه/انتخاب می‌کنی |
| `references/manifest-schema.md` | ساختار manifest.json + 4 JSON Schema | وقتی manifest تولید می‌کنی |
| `scripts/verify_docs.py` | اسکریپت اعتبارسنج مستندات | پس از نوشتن مستند |

## نکات کلیدی برای کیفیت

- **Docs-First**: مستند قبل از کد کامل شود (با معیار پذیرش)
- **No Duplication**: هر حقیقت در یک سند؛ بقیه ارجاع دهند
- **Testable**: هر ادای عملی به Test Case یا Benchmark نگاشت شود
- **Machine-Readable**: بلوک YAML در انتها برای پیمایش ایجنت
- **RFC 2119**: «باید» = 🔒 MUST، «بهتر است» = ⚠️ SHOULD، «می‌تواند» = 💡 MAY

## خطاهای رایج که باید از آن‌ها پرهیز کنی

- ❌ استفاده از `psr-4` به‌عنوان کلید autoload (تَوان از `namespace-map` استفاده می‌کند)
- ❌ وجود `composer.json` در core/ یا engines/ (نقض ZeroDependency)
- ❌ `use Psr\...` در کد (نقض ZeroDependency)
- ❌ نثر انگلیسی برای توضیحات (تَوان فارسی‌اول است)
- ❌ حذف بخشی از قالب موتور بدون `N/A: <دلیل>`
- ❌ DOC-ID تکراری یا ناموجود
- ❌ `*/` داخل کامنت بلوک (پایان زودرس کامنت)
- ❌ `return return` یا سایر الگوهای نحوی شکسته
