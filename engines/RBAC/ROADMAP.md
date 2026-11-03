# نقشه راه تکمیل Tavaan-Engine-RBAC

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-057 |
| Engine ID | `tavaan.engine.rbac` |
| Namespace | `Tavaan\Engine\Rbac` |
| اولویت | **P0** — فاز M4 |
| وابستگی‌ها | `auth`, `database`, `cache`, `event` |
| Facade | `Gate` |
| تخمین LOC | ۱۶۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/rbac.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/rbac.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 12 |
| کلاس‌ها | 12 |
| فایل‌های تست | 1 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 1526 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 2 مورد (0 بالا / 2 متوسط / 0 پایین).

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `hierarchy()` در کلاس `RoleManager` یافت نشد (پیاده‌سازی فعلی: 14 متد). |
| 2 | missing_method | متد `syncFromModules()` در کلاس `RoleManager` یافت نشد (پیاده‌سازی فعلی: 14 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `Gate` | `define()`, `policy()`, `before()`, `after()`, `allows()`, `denies()`, `check()`, `any()`, `authorize()`, `forUser()`, `abilities()` |
| `RoleManager` | `create()`, `assign()`, `remove()`, `hierarchy()`, `syncFromModules()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | assignRole + hasRole |
| `T-02` | hasPermission از نقش |
| `T-03` | سلسله‌مراتب: نقش والد → فرزند ارث می‌برد |
| `T-04` | Policy برای مدل |
| `T-05` | `before` برای super-admin |
| `T-06` | Ownership Scope: کاربر A رکورد B را نمی‌بیند |
| `T-07` | Wildcard `shop.*` |
| `T-08` | Delegation موقت با انقضا |
| `T-09` | Sync از manifest |
| `T-10` | Cache invalidate در تغییر نقش |
| `T-11` | `authorize` → 403 |

## ۶. معیارهای پذیرش

`AC-RBAC-01`, `AC-RBAC-12`, `AC-RBAC-13`

## ۸. جداول دیتابیس

- `tvn_permissions`
- `tvn_roles`
- `tvn_role_user`
- `tvn_permission_role`
- `tvn_delegations`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🟡 — missing_method

**شرح:** متد `hierarchy()` در کلاس `RoleManager` یافت نشد (پیاده‌سازی فعلی: 14 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `hierarchy`.
3. افزودن تست Unit.

### TASK-02 🟡 — missing_method

**شرح:** متد `syncFromModules()` در کلاس `RoleManager` یافت نشد (پیاده‌سازی فعلی: 14 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `syncFromModules`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*