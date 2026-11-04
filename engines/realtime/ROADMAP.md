# نقشه راه تکمیل Tavaan-Engine-Realtime

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-045 |
| Engine ID | `tavaan.engine.realtime` |
| Namespace | `Tavaan\Engine\Realtime` |
| اولویت | **P2** — فاز M6 |
| وابستگی‌ها | `event`, `http`, `queue` |
| Facade | `—` |
| تخمین LOC | ۱۲۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/realtime.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/realtime.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 15 |
| کلاس‌ها | 15 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 2148 |

## ۳. شکاف بین مستندات و پیاده‌سازی

**شکاف‌های شناسایی‌شده:** 2 مورد (1 بالا / 1 متوسط / 0 پایین).

### ۳٫۱ شکاف‌های با اولویت بالا 🔴

| # | نوع | شرح |
|---|------|------|
| 1 | missing_class | کلاس `Broadcast` (با 2 متد عمومی) در پیاده‌سازی یافت نشد. |

### ۳٫۲ شکاف‌های با اولویت متوسط 🟡

| # | نوع | شرح |
|---|------|------|
| 1 | missing_method | متد `presence()` در کلاس `RealtimeManager` یافت نشد (پیاده‌سازی فعلی: 13 متد). |

## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `RealtimeManager` | `broadcast()`, `presence()`, `publish()` |
| `Broadcast` | `channel()`, `connection()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | SSE stream + event format |
| `T-02` | Broadcast از رویداد ShouldBroadcast |
| `T-03` | احراز کانال private |
| `T-04` | WebSocket handshake |
| `T-05` | Frame parse/mask |
| `T-06` | Presence |
| `T-07` | Redis/DB broker fan-out |
| `T-08` | Heartbeat + reconnect |

## ۶. معیارهای پذیرش

`AC-RT-01`, `AC-RT-09`

## ۹. نقشه راه تکمیل (Worklog)

برای رفع شکاف‌های بالا، وظایف زیر به‌ترتیب اولویت پیشنهاد می‌شوند:

### TASK-01 🔴 — missing_class

**شرح:** کلاس `Broadcast` (با 2 متد عمومی) در پیاده‌سازی یافت نشد.

**گام‌ها:**
1. ایجاد فایل `src/Broadcast.php` با namespace صحیح.
2. پیاده‌سازی متدهای: channel, connection.
3. افزودن به `providers` در `module.json`.
4. نوشتن تست‌های Unit برای هر متد.

**معیار تکمیل:**
- همهٔ آیتم‌های AC مرتبط برآورده شود.
- تست‌های متناظر با موفقیت اجرا شوند.

### TASK-02 🟡 — missing_method

**شرح:** متد `presence()` در کلاس `RealtimeManager` یافت نشد (پیاده‌سازی فعلی: 13 متد).

**گام‌ها:**
1. باز کردن کلاس صاحب متد.
2. پیاده‌سازی متد `presence`.
3. افزودن تست Unit.

## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*