# قالب مستند موتور (Engine Specification Template) — ۱۷ بخش اجباری

> کپی کن در `docs/03-engines/<name>.md`. هیچ بخشی حذف نشود مگر با `N/A: <دلیل>`.

```markdown
# DOC-0XX — موتور <Name> | <نام فارسی>

| متادیتا | مقدار |
|---|---|
| DOC-ID | DOC-0XX |
| Engine ID | `tavaan.engine.<name>` |
| Namespace | `Tavaan\Engine\<Pascal>` |
| مسیر | `engines/<kebab-name>/` |
| وضعیت | Draft / Review / Approved v1.0 |
| اولویت | P0 / P1 / P2 |
| فاز ساخت | M1..M8 |
| وابستگی (سخت) | engine-a, engine-b |
| وابستگی (نرم/اختیاری) | engine-c |
| افزونه‌های PHP | ext-x (اجباری) / ext-y (اختیاری + fallback) |
| Facade | `<Name>` → accessor `<name>` |
| جداول DB | `tvn_x`, `...` |
| LOC برآوردی | 0000 |
| مسئول | @owner |
| مرتبط | DOC-xxx, ADR-xxxx |

---

## ۱. هدف و دامنه

### ۱٫۱ هدف (Why)
<یک پاراگراف: چه مسئله‌ای را حل می‌کند و چرا لازم است>

### ۱٫۲ دامنه (What)
- ✅ شامل: <…>
- ❌ خارج از دامنه: <…>

### ۱٫۳ الزامات پوشش‌داده‌شده
| ID | الزام |
|---|---|
| FR-XXX-01 | … |
| NFR-XXX-01 | … |

### ۱٫۴ مقایسه با مراجع
| مرجع | چه چیزی بهتر است | چه چیزی را تغییر دادیم | چرا |
|---|---|---|---|

---

## ۲. مفاهیم و واژگان
| واژه | تعریف |
|---|---|

---

## ۳. معماری داخلی

### ۳٫۱ نمودار اجزا (ASCII/Mermaid)
### ۳٫۲ فهرست کلاس‌ها (کلاس/نوع/مسئولیت/LOC/عمومی؟)
### ۳٫۳ نمودار توالی جریان اصلی
### ۳٫۴ نمودار وضعیت (در صورت وجود State Machine)

---

## ۴. API عمومی

### ۴٫۱ `<MainClass>` — جدول متد/امضا/توضیح/استثنا + نمونهٔ اجراشدنی
### ۴٫۲ Facade — کلاس با @mixin
### ۴٫۳ Helper Functions
### ۴٫۴ Attributes

---

## ۵. الگوریتم‌ها و ساختار داده

### ۵٫۱ <الگوریتم> — هدف/ورودی-خروجی/شبه‌کد/پیچیدگی Big-O/حالت‌های مرزی/استدلال
### ۵٫۲ ساختار دادهٔ داخلی

---

## ۶. پیکربندی
```php
// engines/<name>/config/<name>.php
return [ /* کامل با کامنت */ ];
```
| کلید | نوع | پیش‌فرض | ENV | توضیح |

---

## ۷. قراردادها (Contracts)
```php
interface <Name>Interface { ... }
```
| Interface | پیاده‌سازی پیش‌فرض | جایگزین‌ها | Fake تست |

---

## ۸. نقاط توسعه

### ۸٫۱ رویدادهای منتشرشده
### ۸٫۲ رویدادهای مصرف‌شده
### ۸٫۳ Hook / Extend
### ۸٫۴ درایور سفارشی

---

## ۹. امنیت
| ID | تهدید | کنترل | ارجاع |
### ۹٫۱ چک‌لیست امنیتی موتور (6 آیتم)

---

## ۱۰. کارایی
| عملیات | هدف | روش سنجش |
### ۱۰٫۱ تکنیک‌های بهینه‌سازی
### ۱۰٫۲ نقاط گلوگاه شناخته‌شده

---

## ۱۱. مدیریت خطا
| استثنا | کد | MessageId | HTTP | Retryable |

---

## ۱۲. تست
### ۱۲٫۱ استراتژی (Unit/Feature/Integration/Benchmark/Security)
### ۱۲٫۲ سناریوهای تست (T-01..T-NN)
### ۱۲٫۳ Fixtures

---

## ۱۳. مثال End-to-End (کد اجراشدنی + خروجی مورد انتظار)

---

## ۱۴. معیار پذیرش 🧪
| ID | معیار | تست |

---

## ۱۵. محدودیت‌ها و کارهای آینده
### ۱۵٫۱ محدودیت‌های شناخته‌شده
### ۱۵٫۲ نقشهٔ راه موتور

---

## ۱۶. مراجع
| منبع | چه چیزی الهام‌گرفته شد | چه چیزی متفاوت است |

---

## ۱۷. بلوک ماشین‌خوان 🧠

```yaml
# machine-readable: tavaan.engine.<name>.v1
engine:
  id: tavaan.engine.<name>
  namespace: Tavaan\Engine\<Pascal>
  path: engines/<name>
  priority: P0
  phase: M1
  depends_on: []
  soft_depends_on: []
  extensions:
    required: []
    optional: [{name: x, fallback: y}]
  facade: {name: <Name>, accessor: <name>}
  tables: []
  permissions: []
  events_emitted: []
  events_consumed: []
  config_file: engines/<name>/config/<name>.php
  config_keys: []
  public_api:
    - class: <Name>Engine
      methods: []
  loc_estimate: 0
  acceptance:
    - {id: AC-<NAME>-01, test: 'tests/Unit/…'}
```
```

## راهنمای استفاده برای ایجنت

1. `docs/03-engines/README.md` را بخوان تا Engine ID و فاز را پیدا کنی.
2. این قالب را کپی کن → `docs/03-engines/<name>.md`.
3. بخش‌های ۱ تا ۷ را **قبل از کدنویسی** کامل کن (Docs-First).
4. بخش ۱۴ (معیار پذیرش) را به تست تبدیل کن (TDD).
5. پس از پیاده‌سازی، بخش‌های ۹/۱۰/۱۲ را با نتایج واقعی پر کن.
6. بلوک §۱۷ را دقیق پر کن — ایجنت‌های دیگر از آن استفاده می‌کنند.
