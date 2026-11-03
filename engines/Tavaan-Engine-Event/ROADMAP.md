# نقشه راه تکمیل Tavaan-Engine-Event

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-033 |
| Engine ID | `tavaan.engine.event` |
| Namespace | `Tavaan\Engine\Event` |
| اولویت | **P0** — فاز M1 |
| وابستگی‌ها | `container`, `config`؛ نرم: `queue`, `database` (برای Outbox) |
| Facade | `Event` |
| تخمین LOC | ۹۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/event.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/event.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 12 |
| کلاس‌ها | 12 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 1261 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 1 مورد (1 بالا / 0 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `OutboxRelay` (با 3 متد عمومی) در پیاده‌سازی یافت نشد. |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `EventDispatcher` | `listen()`, `subscribe()`, `dispatch()`, `until()`, `push()`, `hasListeners()`, `getListeners()`, `forget()`, `extend()`, `fake()`, `assertDispatched()` |
| `OutboxRelay` | `relay()`, `retryDead()`, `purge()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | ترتیب Priority قطعی (۳ Listener با priority 100/0/50) |
| `T-02` | Wildcard `shop.order.*` فقط مطابقی‌ها |
| `T-03` | `until()` در اولین پاسخ غیر-null متوقف می‌شود |
| `T-04` | `stopPropagation()` جلوی بقیه را می‌گیرد |
| `T-05` | Listener با خطا → لاگ + ادامه (`stop_on_error=false`) |
| `T-06` | `ShouldQueue` → push به FakeQueue، بدون اجرای همزمان |
| `T-07` | Outbox در تراکنش rollback → ۰ رکورد |
| `T-08` | Outbox در تراکنش commit → ۱ رکورد + Relay → `sent` |
| `T-09` | Inbox: مصرف دوبارهٔ همان key → بدون اثر جانبی |
| `T-10` | ۳ Relay موازی → هر رویداد فقط یک‌بار `sent` |
| `T-11` | پس از `max_attempts` → `dead` + Event |
| `T-12` | `Event::fake()` → `assertDispatched` درست |
| `T-13` | Discovery Cache → Listenerها بدون اسکن ثبت می‌شوند |
| `T-14` | Subscriber با چند رویداد |
| `T-15` | Hierarchical: Listener روی Interface، رویداد پیاده‌سازی را می‌گیرد |

## ۶. معیارهای پذیرش

`AC-EVT-01`, `AC-EVT-11`, `AC-EVT-12`, `AC-EVT-13`

## ۸. جداول دیتابیس

- `tvn_outbox`
- `tvn_inbox`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `OutboxRelay` (با 3 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/OutboxRelay.php` با namespace صحیح.
2. پیاده‌سازی متدهای: relay, retryDead, purge.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*