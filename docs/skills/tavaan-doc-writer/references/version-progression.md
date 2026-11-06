# قواعد نسخه‌گذاری تَوان (Version Progression)

## قاعدهٔ بنیادین

نسخهٔ پروژه با ساختار `MAJOR.MINOR.PATCH` و بر اساس **لایه‌بندی موتورها** تعیین می‌شود:

```
MAJOR.MINOR.PATCH
  │      │      │
  │      │      └── فاز داخل لایه (هر فاز = +1)
  │      └───────── شمارهٔ لایه (L0=0, L1=1, ..., L6=6, Apps=7-9)
  └────────────── 0 تا قبل از انتشار نهایی، 1 برای Release
```

## همسویی لایه ↔ نسخه

| لایه | موتورها | محدودهٔ نسخه | معنا |
|---|---|---|---|
| — (مستندات) | — | `0.0.0` | فاز M0 — فقط مستندات |
| **L0** | container, compat, test | `0.0.x` | پایه — بدون وابستگی |
| **L1** | config, pipeline | `0.1.x` | پیکربندی |
| **L2** | event, crypt, hash, filesystem, i18n, log, cache, ... | `0.2.x` | سرویس‌های بنیادین |
| **L3** | database, http, view, queue, ... | `0.3.x` | زیرساخت |
| **L4** | orm, session, router, migration, ... | `0.4.x` | ابزار داده و وب |
| **L5** | auth, rbac, security, realtime | `0.5.x` | امنیت |
| **L6** | admin, api, workflow | `0.6.x` | کاربرد |
| **Apps-Basic** | Blog, News, Forum | `0.7.x` | اپلیکیشن‌های پایه |
| **Apps-Medium** | Shop, LMS | `0.8.x` | اپلیکیشن‌های متوسط |
| **Apps-Prof** | CRM, ERP, BPMS | `0.9.x` | اپلیکیشن‌های حرفه‌ای |
| **Release** | — | `1.0.0` | انتشار نهایی فریمورک |

## قواعد افزایش (گام‌به‌گام از راست به چپ) 🔒

### قاعدهٔ ۱ — هر فاز = +1 به PATCH
🔒 با پیاده‌سازی هر فاز، رقم سمت راست (PATCH) یک واحد افزایش می‌یابد.

### قاعدهٔ ۲ — انتقال لایه = carry به MINOR
🔒 وقتی لایهٔ N کامل شد و لایهٔ N+1 شروع می‌شود:
- MINOR یک واحد افزایش می‌یابد
- PATCH به 0 بازمی‌گردد

### قاعدهٔ ۳ — بدون پرش از 0.0.10 به 1.0.0 🔒
🔒 نسخه‌ها باید گام‌به‌گام افزایش یابند. هیچ لایه‌ای بیش از ۹ فاز ندارد.

### قاعدهٔ ۴ — انتشار نهایی = carry به MAJOR
🔒 وقتی همهٔ لایه‌ها و اپلیکیشن‌ها کامل شدند:
- MINOR سرریز می‌کند (9 → 10)
- MAJOR یک واحد افزایش می‌یابد (0 → 1)
- MINOR و PATCH به 0 بازمی‌گردند

### قاعدهٔ ۵ — عبور از همهٔ MINORها
🔒 برای رسیدن به 1.0.0، پروژه باید از تمام نسخه‌های MINOR (0.1.0 تا 0.9.0) عبور کند.

## نگاشت کامل فاز ↔ نسخه

| فاز | لایه | نسخه | موتورها/کیت |
|---|---|---|---|
| M0 | — | 0.0.0 | مستندات |
| M1 | L0 | 0.0.1 | container, compat |
| M2 | L0 | 0.0.2 | test |
| M3 | L1 | 0.1.1 | config, pipeline |
| M4 | L2 | 0.2.1 | event |
| M5 | L2 | 0.2.2 | crypt, hash |
| M6 | L2 | 0.2.3 | filesystem, i18n |
| M7 | L2 | 0.2.4 | log |
| M8 | L2 | 0.2.5 | cache |
| M9 | L2 | 0.2.6 | console, module |
| M10 | L2 | 0.2.7 | feature-flag, media, payment, search, tenancy |
| M11 | L3 | 0.3.1 | database |
| M12 | L3 | 0.3.2 | error |
| M13 | L3 | 0.3.3 | http |
| M14 | L3 | 0.3.4 | view, validation, jalali |
| M15 | L3 | 0.3.5 | queue, mail, notification |
| M16 | L3 | 0.3.6 | http-client, observability |
| M17 | L3 | 0.3.7 | optimizer, backup |
| M18 | L4 | 0.4.1 | orm |
| M19 | L4 | 0.4.2 | schema, migration |
| M20 | L4 | 0.4.3 | cookie, cors, session |
| M21 | L4 | 0.4.4 | router |
| M22 | L4 | 0.4.5 | scaffold |
| M23 | L4 | 0.4.6 | schedule, upgrade |
| M24 | L4 | 0.4.7 | audit |
| M25 | L4 | 0.4.8 | plugin, report |
| M26 | L5 | 0.5.1 | auth |
| M27 | L5 | 0.5.2 | rbac |
| M28 | L5 | 0.5.3 | security |
| M29 | L5 | 0.5.4 | realtime |
| M30 | L6 | 0.6.1 | api |
| M31 | L6 | 0.6.2 | workflow |
| M32 | L6 | 0.6.3 | admin |
| M33 | App | 0.7.1 | Blog kit |
| M34 | App | 0.7.2 | News-Portal kit |
| M35 | App | 0.7.3 | Forum kit |
| M36 | App | 0.8.1 | Shop kit (فاز ۱) |
| M37 | App | 0.8.2 | Shop kit (فاز ۲) |
| M38 | App | 0.8.3 | LMS kit |
| M39 | App | 0.9.1 | CRM kit |
| M40 | App | 0.9.2 | ERP kit (فاز ۱) |
| M41 | App | 0.9.3 | ERP kit (فاز ۲) |
| M42 | App | 0.9.4 | BPMS kit (فاز ۱) |
| M43 | App | 0.9.5 | BPMS kit (فاز ۲) |
| M44 | Release | 1.0.0 | انتشار فریمورک |

## مسیر بحرانی

```
container → config → log → database → orm → rbac → admin
  L0         L1       L2      L3        L4     L5      L6
  M1         M3       M7      M11       M18    M27     M32
```

این ۷ موتور، مسیر بحرانی هستند.

## چرا این طرح از کد اسپاگتی جلوگیری می‌کند؟

۱. هر لایه فقط به لایه‌های قبلی وابسته است
۲. هر فاز کوچک است (۱-۵ موتور)
۳. نسخه نشان‌دهندهٔ پیشرفت واقعی است
۴. انتقال لایه = نسخهٔ پایه
۵. Release 1.0.0 فقط پس از عبور از همهٔ لایه‌ها
