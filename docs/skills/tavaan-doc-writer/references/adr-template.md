# قالب ADR (Architecture Decision Record) — ۷ بخش

```markdown
# ADR-NNNN — <عنوان تصمیم>

| متادیتا | مقدار |
|---|---|
| شماره | ADR-NNNN |
| وضعیت | Proposed / Accepted / Deprecated / Superseded by ADR-XXXX |
| تاریخ | YYYY-MM-DD |
| تصمیم‌گیرندگان | @name1, @name2 |
| مرتبط | DOC-XXX, ADR-YYYY |
| سطح اثر | Kernel / Engine / Module / Global |

## ۱. زمینه (Context)
<چه مسئله‌ای وجود دارد؟ چه نیروهایی در بازی‌اند؟ چه محدودیت‌هایی؟>

## ۲. تصمیم (Decision)
<تصمیم دقیقاً چیست؟ به‌صورت فعال و قاطع بنویسید.>

## ۳. گزینه‌های بررسی‌شده (Options Considered)
| گزینه | مزایا | معایب |
|---|---|---|
| A | … | … |
| B | … | … |
| C (انتخاب‌شده) | … | … |

## ۴. دلایل انتخاب (Rationale)
<چرا این گزینه؟ به کدام اصل (P1-P12) تکیه می‌کند؟>

## ۵. پیامدها (Consequences)
### مثبت
- …
### منفی / هزینه
- …
### ریسک‌ها
- …

## ۶. انطباق (Compliance)
<چه چیزهایی باید تغییر کنند تا با این تصمیم سازگار شوند؟>

## ۷. اعتبارسنجی (Validation) 🧪
<چگونه بدانیم تصمیم درست کار می‌کند؟ معیار/تست.>
```

## قواعد ADR 🔒

1. **شمارهٔ ترتیبی** هرگز تغییر نمی‌کند (حتی اگر تصمیم لغو شود → `Superseded`).
2. ADRهای پذیرفته‌شده **تغییر نمی‌کنند**؛ اصلاح با ADR جدید.
3. هر ADR باید به **اصل (P1-P12) یا الزام (FR-/NFR-/CON-)** ارجاع دهد.
4. وضعیت `Proposed` تا زمانی که تیم تصویب نکند.
5. ADRها در `docs/adr/` با نام `ADR-NNNN-<slug>.md`.

## فهرست ۱۶ ADR مرجع

| شماره | عنوان | وضعیت | فایل |
|---|---|---|---|
| ADR-0001 | معماری ترکیبی | Accepted | combined-architecture |
| ADR-0002 | مدل مجوزدهی | Proposed | licensing |
| ADR-0003 | حداقل PHP 8.2 | Accepted | php-min-version |
| ADR-0004 | صفر وابستگی | Accepted | zero-dependency |
| ADR-0005 | Autoloader خودنوشته | Accepted | custom-autoloader |
| ADR-0006 | ULID کلید اصلی | Accepted | primary-key-ulid |
| ADR-0007 | ORM دوگانه | Accepted | dual-orm |
| ADR-0008 | پسوند View .tv.php | Accepted | view-extension |
| ADR-0009 | الگوی Outbox | Accepted | outbox-pattern |
| ADR-0010 | Multi-Tenancy | Accepted | multi-tenancy |
| ADR-0011 | Fiber-Aware Context | Accepted | fiber-context |
| ADR-0012 | تقویم جلالی | Accepted | jalali-calendar |
| ADR-0013 | Money با int minor-unit | Accepted | money-representation |
| ADR-0014 | صفر vendor در runtime | Accepted | no-vendor-runtime |
| ADR-0015 | نسخه‌گذاری API | Accepted | api-versioning |
| ADR-0016 | Lazy Proxy بدون eval | Accepted | lazy-proxy-no-eval |
