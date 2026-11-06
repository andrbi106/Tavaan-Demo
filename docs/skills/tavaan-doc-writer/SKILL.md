---
name: tavaan-doc-writer
description: >-
  Skill اختصاصی برای ممیزی، مقایسه و بازنویسی مستندات پروژه‌های مبتنی بر فریمورک تَوان (Tavaan).
  این skill ساختار مستندات مرجع (۱۳ بخش شماره‌گذاری‌شده، ADRها، قالب‌ها، schemaها)،
  کنوانسیون‌ها (DOC-ID، DOC-XXX-SNN برای فایل‌های ممیزی، جدول متادیتا، نمادهای 🔒⚠️💡🧪🧠، RFC 2119)،
  قوانین سخت (R1-R10)، اصول بنیادین (P1-P12)، لایه‌بندی موتورها (L0-L6)،
  نسخه‌گذاری همسو با لایه (0.0.0 تا 1.0.0 در ۴۵ فاز M0-M44)،
  و قالب‌های بسط‌یافتهٔ موتور (۱۸ فایل)، کیت (۱۳ فایل)، ماژول (۱۶ فایل) و افزونه (۱۵ فایل) را در اختیار می‌گذارد.
  از این skill برای نوشتن مستندات جدید تَوان، ممیزی انطباق پروژه با اصول ZeroDependency،
  شناسایی تفاوت‌ها و تناقضات بین کد و مستندات، و تولید manifest ماشین‌خوان استفاده کنید.
  این skill را هر زمان که کاربر از تَوان، Tavaan، مستندات فریمورک فارسی، ZeroDependency،
  ممیزی پروژه PHP، یا ساخت مستندات موتور/کیت/ماژول/افزونه/ADR می‌گوید، فعال کنید.
---

# Tavaan Doc Writer — Skill ممیزی و مستندنویسی تَوان

## نقش این skill

این skill حاصلِ مطالعهٔ کامل مخزن مرجع `Derogaramin/Tavaan-Doc` و بازنویسی بر اساس پروژهٔ واقعی `Tavaan-Demo` است.
هدف: توانایی بازتولید مستندات تَوان با حفظ کنوانسیون‌ها، و ممیزی پروژه‌های تَوان برای انطباق.

## هویت تَوان

| ویژگی | مقدار |
|---|---|
| نام فارسی | تَوان |
| نام لاتین | Tavaan |
| شعار | صفر وابستگی، بی‌نهایت امکان / Zero Dependency. Infinite Capability. |
| نسخهٔ فعلی پروژه | 0.2.7 (L2 کامل) |
| حداقل PHP | 8.2.0 |
| سیاست وابستگی | zero-external (هیچ پکیج بیرونی در core/ و engines/) |
| معماری‌ها | layered, hmvc, modular-monolith, event-driven, hexagonal |
| لایه‌های موتور | ۷ لایه (L0-L6) بر اساس DEPENDENCIES.md |

---

## ساختار مستندات (۱۳ بخش + ADR + قالب + schema)

```
docs/
├── README.md                  DOC-000 — شاخص ناوبری
├── manifest.json              🧠 فهرست ماشین‌خوان (۱۳۳۱+ سند)
├── 00-overview/               اهداف، اصول، الزامات، شروع سریع
├── 01-standards/              PHP 8.2، سبک کد، نام‌گذاری، خطا، قراردادها، مستندسازی، امنیت
├── 02-architecture/           معماری + لایه‌بندی + چرخهٔ عمر
├── 03-engines/                ۵۲ پوشهٔ موتور (هر کدام ۱۸ فایل ممیزی)
├── 04-structure/              folder-structure, cli-reference, config-reference
├── 05-applications/           ۸ پوشهٔ کیت (هر کدام ۱۳ فایل ممیزی)
├── 07-quality/                testing-strategy, definition-of-done, audit-report
├── 08-roadmap/                ۷ سند: milestones (۴۵ فاز), phase-audit, phase-dependencies,
│                              version-progression, agent-backlog, foundation-phases, execution-phases
├── 09-references/             glossary (واژه‌نامه فارسی↔لاتین)
├── 10-modules/                ۱۶ پوشهٔ ماژول (هر کدام ۱۶ فایل ممیزی)
├── 11-plugins/                ۴ پوشهٔ افزونه نمونه (هر کدام ۱۵ فایل ممیزی)
├── adr/                       شاخص ADR + ADRهای پیاده‌سازی‌شده
├── schemas/                   JSON Schema: project.v1
└── templates/                 engine-spec-template, adr-template
tools/scripts/                 verify_docs.py, audit_tavaan.py
skills/tavaan-doc-writer/      این skill
```

---

## لایه‌بندی موتورها (L0-L6)

بر اساس `DEPENDENCIES.md` در مخزن Tavaan-Demo، موتورها در ۷ لایه چیده شده‌اند:

| لایه | موتورها | تعداد |
|---|---|---|
| **L0** | container, compat, test | 3 |
| **L1** | config, pipeline | 2 |
| **L2** | event, crypt, hash, filesystem, i18n, log, cache, feature-flag, media, payment, search, tenancy, console, module | 14 |
| **L3** | database, error, http, http-client, mail, notification, queue, view, validation, jalali, optimizer, observability, backup | 13 |
| **L4** | orm, schema, cookie, cors, session, router, migration, scaffold, schedule, upgrade, audit, plugin, report | 13 |
| **L5** | auth, rbac, security, realtime | 4 |
| **L6** | admin, api, workflow | 3 |

**قاعده:** هر لایه فقط به لایه‌های قبلی وابسته است.

---

## نسخه‌گذاری همسو با لایه (DOC-126)

نسخهٔ پروژه با ساختار `MAJOR.MINOR.PATCH` و بر اساس لایه‌بندی تعیین می‌شود:

```
MAJOR.MINOR.PATCH
  │      │      │
  │      │      └── فاز داخل لایه (هر فاز = +1)
  │      └───────── شمارهٔ لایه (L0=0, L1=1, ..., L6=6, Apps=7-9)
  └────────────── 0 تا Release، سپس 1
```

| لایه | نسخه | فازها |
|---|---|---|
| — (مستندات) | 0.0.0 | M0 |
| L0 | 0.0.x | M1-M2 |
| L1 | 0.1.x | M3 |
| L2 | 0.2.x | M4-M10 |
| L3 | 0.3.x | M11-M17 |
| L4 | 0.4.x | M18-M25 |
| L5 | 0.5.x | M26-M29 |
| L6 | 0.6.x | M30-M32 |
| Apps-Basic | 0.7.x | M33-M35 |
| Apps-Medium | 0.8.x | M36-M38 |
| Apps-Prof | 0.9.x | M39-M43 |
| Release | 1.0.0 | M44 |

**قواعد افزایش (گام‌به‌گام از راست به چپ):**
۱. هر فاز = +1 به PATCH
۲. انتقال لایه = carry به MINOR (PATCH → 0)
۳. انتشار نهایی = carry از minor overflow → MAJOR +1
۴. عبور از همهٔ MINORها (0 تا 9) — بدون پرش

---

## فازهای اجرایی (۴۵ فاز، M0-M44)

| گروه | فازها | نسخه | وضعیت |
|---|---|---|---|
| مستندات | M0 | 0.0.0 | ✅ |
| L0-L2 (بنیاد) | M1-M10 | 0.0.1-0.2.7 | ✅ |
| L3 (زیرساخت) | M11-M17 | 0.3.1-0.3.7 | ⚠️ اسکلت |
| L4 (ابزار) | M18-M25 | 0.4.1-0.4.8 | ⚠️ اسکلت |
| L5 (امنیت) | M26-M29 | 0.5.1-0.5.4 | ⚠️ اسکلت |
| L6 (کاربرد) | M30-M32 | 0.6.1-0.6.3 | ⚠️ اسکلت |
| Apps-Basic | M33-M35 | 0.7.1-0.7.3 | ⬜ |
| Apps-Medium | M36-M38 | 0.8.1-0.8.3 | ⬜ |
| Apps-Prof | M39-M43 | 0.9.1-0.9.5 | ⬜ |
| Release | M44 | 1.0.0 | ⬜ |

---

## کنوانسیون‌های اجباری

### ۱) شناسهٔ DOC-ID

هر سند دارای شناسهٔ یکتا است:

| محدوده | نوع | توضیح |
|---|---|---|
| DOC-000 | شاخص | ناوبری |
| DOC-001..004 | overview | اهداف، اصول، الزامات، شروع سریع |
| DOC-010..016 | standards | PHP 8.2، سبک، نام‌گذاری، خطا، قرارداد، مستندسازی، امنیت |
| DOC-020..028 | architecture | معماری |
| DOC-030 | رجیستری موتورها | شاخص ۵۲ موتور |
| DOC-031..082 | موتورها | هر موتور یک پوشه با ۱۸ فایل |
| DOC-080..086 | structure | ساختار، CLI، config |
| DOC-090 | رجیستری کیت‌ها | شاخص ۸ کیت |
| DOC-091..098 | کیت‌ها | هر کیت یک پوشه با ۱۳ فایل |
| DOC-110..113 | quality | تست، DoD، ممیزی |
| DOC-120..126 | roadmap | milestones, phase-audit, phase-deps, version-prog, agent-backlog |
| DOC-131 | references | واژه‌نامه |
| DOC-140 | شاخص ADR | |
| DOC-150..152 | قالب‌ها | engine-spec, adr |
| DOC-160 | رجیستری ماژول‌ها | شاخص ۱۶ ماژول |
| DOC-161..176 | ماژول‌ها | هر ماژول یک پوشه با ۱۶ فایل |
| DOC-170 | رجیستری افزونه‌ها | شاخص ۴ افزونه |
| DOC-171..174 | افزونه‌ها | هر افزونه یک پوشه با ۱۵ فایل |
| ADR-NNNN | تصمیمات معماری | ترتیبی، غیرقابل تغییر |

### ۲) شناسهٔ فایل‌های ممیزی (DOC-XXX-SNN)

هر فایل ممیزی در پوشهٔ موتور/کیت/ماژول/افزونه دارای شناسهٔ یکتا با پسوند بخش است:

```
DOC-XXX-SNN
  │      │
  │      └── شمارهٔ بخش (۲ رقمی لاتین، zero-padded)
  └───────── شناسهٔ entity (DOC-034 برای auth)
```

**مثال‌ها:**
- `DOC-034-S01` — موتور auth، بخش ۰۱ (هدف و دامنه)
- `DOC-034-S17` — موتور auth، بخش ۱۷ (بلوک ماشین‌خوان)
- `DOC-094-S03` — کیت shop، بخش ۰۳ (مدل داده)
- `DOC-165-S08` — ماژول shop، بخش ۰۸ (پایگاه داده)
- `DOC-172-S06` — افزونه payment-gateway، بخش ۰۶ (Hookها)

### ۳) جدول متادیتا (بالای هر سند)
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

برای فایل ممیزی بخش:
```markdown
# DOC-0XX-SNN — <عنوان بخش> (<نوع> <slug>)

| متادیتا | مقدار |
|---|---|
| DOC-ID | DOC-0XX-SNN |
| موتور/کیت/ماژول/افزونه | `<slug>` |
| بخش | NN — <عنوان> |
| وضعیت | Draft v0.0.1 |
| نوع | فایل ممیزی عملیاتی |
```

### ۴) نمادها (RFC 2119)
| نماد | معنا | معادل RFC 2119 |
|---|---|---|
| 🔒 | الزامی — نقض = ریجکت کد | MUST / MUST NOT |
| ⚠️ | توصیهٔ قوی | SHOULD |
| 💡 | اختیاری/بهتر است | MAY |
| 🧪 | دارای معیار تست/پذیرش | — |
| 🧠 | دارای بلوک ماشین‌خوان (YAML) | — |

### ۵) دوزبانگی
- **نثر فارسی** برای توضیحات (RTL)
- **شناسه‌های لاتین** برای: namespace، کلاس، متد، فایل، کلید config، ENV، CLI، مسیر
- اولین occurrence: `واژهٔ فارسی (Latin)`، سپس فقط فارسی

### ۶) شماره‌گذاری بخش‌ها
- اعداد فارسی: `۱.`, `۲.`, `۳.`
- زیربخش‌ها با `٫` (U+066B): `۱٫۱`, `۱٫۲`

---

## ۱۰ قانون سخت (R1-R10)

```
R1  هر فایل PHP با declare(strict_types=1); شروع می‌شود.
R2  هیچ require/include دستی در کد کاربردی؛ فقط Autoloader هسته.
R3  هیچ پکیج خارجی در core/ و engines/ ممنوع است (ZeroDependency).
R4  هیچ Property پویا.
R5  هر خروجی عمومی باید Interface داشته باشد.
R6  هر موتور: Provider + Interface + Tests + Docs + Benchmark.
R7  نام namespace فقط از ریشهٔ ثبت‌شده.
R8  خطاها استثنا هستند با کد عددی و MessageId.
R9  هیچ کوئری با الحاق رشته.
R10 قبل از Merge: php -l + PHPStan max + تست‌ها + Benchmark.
```

---

## ۱۲ اصل بنیادین (P1-P12)

| # | اصل | خلاصه |
|---|---|---|
| P1 | Zero Dependency | هیچ پکیج بیرونی در core/engines |
| P2 | Contract-First | Interface قبل از پیاده‌سازی |
| P3 | Minimal Kernel | هسته = حداقل |
| P4 | Explicit Boundaries | مرز ماژول؛ فقط درگاه عمومی |
| P5 | Events over Calls | بین دامنه‌ها با رویداد |
| P6 | Fail Fast & Loud | بدون catch خالی |
| P7 | Secure by Default | auto-escape، CSRF |
| P8 | No Hidden Magic | Facade = Lazy Proxy + @mixin |
| P9 | SRP + Small Surface | کلاس ≤۳۰۰، متد ≤۵۰ |
| P10 | Readable Performance | بهینه‌سازی با اندازه‌گیری |
| P11 | Language Forward-Compat | 8.2 تا 8.4 بدون Deprecated |
| P12 | Extensibility Triad | Hook + Contract + Decorator |

**ترتیب قطع‌کننده:** Correctness > Security > Simplicity > Performance > Flexibility > Convenience

---

## قالب‌های بسط‌یافته (پوشه + چند فایل)

هر entity (موتور/کیت/ماژول/افزونه) به‌جای یک فایل، یک **پوشه** با چند فایل ممیزی است.

### موتور — ۱۸ فایل (README + ۱۷ بخش)
```
docs/03-engines/<name>/
├── README.md                    DOC-XXX
├── 01-goal-scope.md             DOC-XXX-S01
├── 02-concepts.md               DOC-XXX-S02
├── 03-architecture.md           DOC-XXX-S03
├── 04-public-api.md             DOC-XXX-S04
├── 05-algorithms.md             DOC-XXX-S05
├── 06-config.md                 DOC-XXX-S06
├── 07-contracts.md              DOC-XXX-S07
├── 08-extensibility.md          DOC-XXX-S08
├── 09-security.md               DOC-XXX-S09
├── 10-performance.md            DOC-XXX-S10
├── 11-error-handling.md         DOC-XXX-S11
├── 12-testing.md                DOC-XXX-S12
├── 13-example.md                DOC-XXX-S13
├── 14-acceptance.md             DOC-XXX-S14
├── 15-limitations.md            DOC-XXX-S15
├── 16-references.md             DOC-XXX-S16
└── 17-machine-readable.yml      DOC-XXX-S17
```

### کیت — ۱۳ فایل (README + ۱۲ بخش)
```
docs/05-applications/<name>/
├── README.md                    DOC-0XX
├── 01-domain.md                 DOC-0XX-S01
├── 02-module-composition.md     DOC-0XX-S02
├── 03-data-model.md             DOC-0XX-S03
├── 04-user-journeys.md          DOC-0XX-S04
├── 05-roles-permissions.md      DOC-0XX-S05
├── 06-events.md                 DOC-0XX-S06
├── 07-api.md                    DOC-0XX-S07
├── 08-advanced-features.md      DOC-0XX-S08
├── 09-performance.md            DOC-0XX-S09
├── 10-e2e-tests.md              DOC-0XX-S10
├── 11-acceptance.md             DOC-0XX-S11
└── 12-machine-readable.md       DOC-0XX-S12
```

### ماژول — ۱۶ فایل (README + ۱۵ بخش)
```
docs/10-modules/<name>/
├── README.md                    DOC-XXX
├── 01-goal-scope.md             DOC-XXX-S01
├── 02-dependencies.md           DOC-XXX-S02
├── 03-structure.md              DOC-XXX-S03
├── 04-public-api.md             DOC-XXX-S04
├── 05-config.md                 DOC-XXX-S05
├── 06-contracts.md              DOC-XXX-S06
├── 07-events.md                 DOC-XXX-S07
├── 08-database.md               DOC-XXX-S08
├── 09-permissions.md            DOC-XXX-S09
├── 10-routes.md                 DOC-XXX-S10
├── 11-views.md                  DOC-XXX-S11
├── 12-tests.md                  DOC-XXX-S12
├── 13-example.md                DOC-XXX-S13
├── 14-acceptance.md             DOC-XXX-S14
└── 15-machine-readable.md       DOC-XXX-S15
```

### افزونه — ۱۵ فایل (README + ۱۴ بخش)
```
docs/11-plugins/<name>/
├── README.md                    DOC-XXX
├── 01-goal-scope.md             DOC-XXX-S01
├── 02-manifest.md               DOC-XXX-S02
├── 03-namespace-autoload.md     DOC-XXX-S03
├── 04-provider.md               DOC-XXX-S04
├── 05-permissions-capabilities.md  DOC-XXX-S05
├── 06-hooks.md                  DOC-XXX-S06
├── 07-signature.md              DOC-XXX-S07
├── 08-config.md                 DOC-XXX-S08
├── 09-database.md               DOC-XXX-S09
├── 10-events.md                 DOC-XXX-S10
├── 11-installation.md           DOC-XXX-S11
├── 12-tests.md                  DOC-XXX-S12
├── 13-acceptance.md             DOC-XXX-S13
└── 14-machine-readable.md       DOC-XXX-S14
```

### ساختار هر فایل ممیزی عملیاتی

هر فایل ممیزی شامل:
۱. **شناسهٔ یکتا** — `DOC-XXX-SNN`
۲. **جدول متادیتا** — DOC-ID، entity، بخش، وضعیت، نوع
۳. **محتوای فعلی** (در صورت وجود) یا **شرح بخش**
۴. **چک‌لیست ممیزی عملیاتی** — ۳-۶ آیتم قابل‌اجرا
۵. **جدول وضعیت ممیزی** — شاخص/وضعیت
۶. **بخش یادداشت ممیزی‌کننده** — تاریخ/ممیزی‌کننده/یادداشت

---

## قالب ADR (۷ بخش)

```
# ADR-NNNN — <عنوان>
## ۱. زمینه (Context)
## ۲. تصمیم (Decision)
## ۳. گزینه‌های بررسی‌شده (Options) — جدول A/B/C-selected
## ۴. دلایل انتخاب (Rationale) — ارجاع به اصل
## ۵. پیامدها (Consequences) — مثبت/منفی/ریسک
## ۶. انطباق (Compliance)
## ۷. اعتبارسنجی (Validation) 🧪
```

قواعد ADR: شماره ترتیبی هرگز تغییر نمی‌کند؛ ADR پذیرفته‌شده تغییر نمی‌کند؛ هر ADR باید به اصل ارجاع دهد.

### ADRهای پیاده‌سازی‌شده در Tavaan-Demo
| شماره | عنوان | وضعیت |
|---|---|---|
| ADR-0004 | صفر وابستگی در هسته | ✅ Accepted |
| ADR-0005 | Autoloader خودنوشته | ✅ Accepted |

### ADRهای مرجع (طراحی‌شده)
ADR-0001 تا ADR-0016 (در مخزن مرجع Derogaramin/Tavaan-Doc).

---

## فرآیند ممیزی پروژه (Audit Workflow)

وقتی کاربر می‌گوید «پروژه را بررسی کن»:

### مرحلهٔ ۱: بررسی قوانین سخت (R1-R10)
- R1: `grep -rL "declare(strict_types=1)" --include="*.php"` → فایل‌های بدون strict_types
- R3: `find . -name composer.json` در core/engines + `grep -r "use Psr\\\\"` + `grep '"psr-4"'`
- R8: `grep -rn "throw new \\\\Exception\|throw new \\\\RuntimeException"` → استثنای خام

### مرحلهٔ ۲: بررسی لایه‌بندی
- مقایسه با DEPENDENCIES.md
- هر لایه فقط به لایه‌های قبلی وابسته است
- عمق وابستگی ≤ ۵

### مرحلهٔ ۳: بررسی نسخه‌گذاری
- نسخه با لایه همسو است (L0→0.0.x, L1→0.1.x, ...)
- افزایش گام‌به‌گام (بدون پرش)
- هر فاز = +1 PATCH

### مرحلهٔ ۴: بررسی مستندات بسط‌یافته
- هر موتور = پوشه با ۱۸ فایل
- هر کیت = پوشه با ۱۳ فایل
- هر ماژول = پوشه با ۱۶ فایل
- هر افزونه = پوشه با ۱۵ فایل
- DOC-ID یکتا + DOC-XXX-SNN برای فایل‌های ممیزی
- چک‌لیست ممیزی در هر فایل تکمیل شده

### مرحلهٔ ۵: تولید گزارش
قالب گزارش در `references/audit-checklist.md`.

---

## فرآیند تولید مستندات (Doc Generation Workflow)

وقتی کاربر می‌گوید «مستندات بنویس»:

۱. **تعیین نوع entity**: موتور / کیت / ماژول / افزونه / ADR / overview / standard
۲. **تعیین DOC-ID** بعدی از محدودهٔ مربوط
۳. **ایجاد پوشه** `docs/<section>/<name>/`
۴. **تولید README.md** با جدول متادیتا + فهرست فایل‌ها
۵. **تولید فایل‌های ممیزی** (NN-<slug>.md) هر کدام با:
   - شناسهٔ `DOC-XXX-SNN`
   - جدول متادیتا
   - شرح بخش یا محتوای فعلی
   - چک‌لیست ممیزی عملیاتی
   - جدول وضعیت ممیزی
۶. **به‌روزرسانی رجیستری** (README.md بخش مربوط)
۷. **به‌روزرسانی manifest.json**
۸. **اجرای `verify_docs.py`** برای اعتبارسنجی

---

## فایل‌های مرجع

| فایل | محتوا | چه زمانی بخوان |
|---|---|---|
| `references/engine-template.md` | قالب ۱۷بخشی موتور | وقتی مستند موتور می‌نویسی |
| `references/extensions-templates.md` | قالب‌های کیت (۱۲بخش) + ماژول (۱۵بخش) + افزونه (۱۴بخش) | وقتی کیت/ماژول/افزونه می‌نویسی |
| `references/adr-template.md` | قالب ۷بخشی ADR + فهرست ADRها | وقتی ADR می‌نویسی |
| `references/conventions.md` | کنوانسیون‌های نام‌گذاری + واژه‌نامه | وقتی نام‌گذاری می‌کنی |
| `references/folder-structure.md` | ساختار کامل پوشه‌ها + لایه‌بندی | وقتی ساختار بررسی می‌کنی |
| `references/audit-checklist.md` | چک‌لیست ممیزی R1-R10 + P1-P12 + لایه‌بندی | وقتی پروژه ممیزی می‌کنی |
| `references/version-progression.md` | قواعد نسخه‌گذاری + همسویی لایه | وقتی نسخه تعیین می‌کنی |
| `references/manifest-schema.md` | ساختار manifest.json | وقتی manifest تولید می‌کنی |
| `scripts/verify_docs.py` | اسکریپت اعتبارسنج مستندات | پس از نوشتن مستند |

---

## نکات کلیدی برای کیفیت

- **Docs-First**: مستند قبل از کد کامل شود (با معیار پذیرش)
- **بسط به پوشه**: هر entity = پوشه با چند فایل ممیزی (نه یک فایل)
- **شناسهٔ یکتا**: هر فایل `DOC-XXX-SNN` با اعداد لاتین zero-padded
- **چک‌لیست عملیاتی**: هر فایل ممیزی دارای چک‌لیست قابل‌اجرا
- **No Duplication**: هر حقیقت در یک سند
- **Machine-Readable**: بلوک YAML در انتها
- **RFC 2119**: «باید» = 🔒 MUST، «بهتر است» = ⚠️ SHOULD، «می‌تواند» = 💡 MAY

---

## خطاهای رایج که باید از آن‌ها پرهیز کنی

- ❌ استفاده از `psr-4` به‌عنوان کلید autoload (تَوان از `namespace-map` استفاده می‌کند)
- ❌ وجود `composer.json` در core/ یا engines/ (نقض ZeroDependency)
- ❌ `use Psr\...` در کد (نقض ZeroDependency)
- ❌ نثر انگلیسی برای توضیحات (تَوان فارسی‌اول است)
- ❌ حذف بخشی از قالب بدون `N/A: <دلیل>`
- ❌ DOC-ID تکراری یا ناموجود
- ❌ `*/` داخل کامنت بلوک (پایان زودرس کامنت)
- ❌ `return return` یا سایر الگوهای نحوی شکسته
- ❌ نسخه با پرش (مثلاً 0.0.9 → 1.0.0) — باید گام‌به‌گام
- ❌ موتور در لایهٔ N که به لایهٔ N+1 وابسته است (نقض لایه‌بندی)
- ❌ فایل ممیزی بدون شناسهٔ `DOC-XXX-SNN`
- ❌ اعداد فارسی در شناسه (از لاتین zero-padded استفاده کن)
