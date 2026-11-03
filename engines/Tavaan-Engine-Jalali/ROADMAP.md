# نقشه راه تکمیل Tavaan-Engine-Jalali

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-070 |
| Engine ID | `tavaan.engine.jalali` |
| Namespace | `Tavaan\Engine\I18n\Jalali` |
| اولویت | **P0** — فاز M2 |
| وابستگی‌ها | `i18n` |
| Facade | `—` |
| تخمین LOC | ۸۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/jalali.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/jalali.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 0 |
| کلاس‌ها | 0 |
| فایل‌های تست | 0 |
| فایل‌های پیکربندی | 0 |
| migrationها | 0 |
| LOC واقعی | 0 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 5 مورد (5 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `JalaliDate` (با 13 متد عمومی) در پیاده‌سازی یافت نشد. |
| 2 | missing_class | کلاس `Jalali` (با 5 متد عمومی) در پیاده‌سازی یافت نشد. |
| 3 | missing_class | کلاس `Clock` (با 3 متد عمومی) در پیاده‌سازی یافت نشد. |
| 4 | missing_tests | مستندات 10 سناریوی تست (T-NN) تعریف می‌کند اما هیچ فایل تستی در `tests/` وجود ندارد. |
| 5 | missing_config | هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `JalaliDate` | `now()`, `fromGregorian()`, `parse()`, `toGregorian()`, `format()`, `addDays()`, `subMonths()`, `diffInDays()`, `isLeap()`, `monthName()`, `dayName()`, `startOfMonth()`, `endOfMonth()` |
| `Jalali` | `toJalali()`, `toGregorian()`, `isLeap()`, `monthLength()`, `format()` |
| `Clock` | `utcNow()`, `now()`, `jalaliNow()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | ۱۰۰۰۰ تاریخ تصادفی: j2g(g2j(d)) == d |
| `T-02` | مرزهای کبیسه: ۱۳۹۹ (کبیسه)، ۱۴۰۰، ۱۴۰۳ (کبیسه) |
| `T-03` | اسفند کبیسه = ۳۰ روز |
| `T-04` | تطبیق با `intl` (در صورت وجود) روی ۱۰۰۰۰ نمونه |
| `T-05` | تطبیق با `jdf`/`jalaali-js` روی نمونه‌های مرجع |
| `T-06` | نام ماه/روز |
| `T-07` | فرمت با ارقام فارسی |
| `T-08` | qمری ↔ میلادی |
| `T-09` | `diffInDays` دوطرفه |
| `T-10` | startOfMonth/endOfMonth |

## ۶. معیارهای پذیرش

`AC-JALALI-01`, `AC-JALALI-02`, `AC-JALALI-03`, `AC-JALALI-04`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `JalaliDate` (با 13 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/JalaliDate.php` با namespace صحیح.
2. پیاده‌سازی متدهای: now, fromGregorian, parse, toGregorian, format, addDays, subMonths, diffInDays, isLeap, monthName, dayName, startOfMonth, endOfMonth.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🔴 — missing_class

**شرح:** کلاس `Jalali` (با 5 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Jalali.php` با namespace صحیح.
2. پیاده‌سازی متدهای: toJalali, toGregorian, isLeap, monthLength, format.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-03 🔴 — missing_class

**شرح:** کلاس `Clock` (با 3 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Clock.php` با namespace صحیح.
2. پیاده‌سازی متدهای: utcNow, now, jalaliNow.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-04 🔴 — missing_tests

**شرح:** مستندات 10 سناریوی تست (T-NN) تعریف می‌کند اما هیچ فایل تستی در `tests/` وجود ندارد.

**گام‌ها:**
1. ایجاد `tests/Feature/<Engine>Test.php`.
2. نوشتن متد `test<Scenario>` برای هر T-NN در مستندات.
3. اجرای `php bin/tvn test` و رسیدن به پوشش کامل.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-05 🔴 — missing_config

**شرح:** هیچ فایل پیکربندی در `config/` وجود ندارد؛ برخلاف قاعده R6 (هر موتور باید `config/<name>.php` داشته باشد).

**گام‌ها:**
1. ایجاد `config/<engine>.php` با همهٔ کلیدهای مستندشده.
2. افزودن به `config` در `module.json`.
3. تست با `php bin/tvn env:check`.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*