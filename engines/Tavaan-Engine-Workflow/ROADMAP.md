# نقشه راه تکمیل Tavaan-Engine-Workflow

> این سند به‌صورت خودکار از مقایسهٔ پیاده‌سازی فعلی با مستندات رسمی موتور در `Derogaramin/Tavaan-Doc` تولید شده است.

## ۱. مرجع مستندات

| فیلد | مقدار |
|------|------|
| DOC-ID | DOC-071 |
| Engine ID | `tavaan.engine.workflow` |
| Namespace | `Tavaan\Engine\Workflow` |
| اولویت | **P2** — فاز M7 |
| وابستگی‌ها | `event`, `database`, `rbac`, `queue` |
| Facade | `Workflow` |
| تخمین LOC | ۲۰۰۰ |
| سند مرجع | [`Derogaramin/Tavaan-Doc/docs/03-engines/workflow.md`](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/workflow.md) |

## ۲. خلاصهٔ وضعیت فعلی

| شاخص | مقدار |
|------|------|
| فایل‌های src | 31 |
| کلاس‌ها | 31 |
| فایل‌های تست | 2 |
| فایل‌های پیکربندی | 1 |
| migrationها | 1 |
| LOC واقعی | 3807 |

## ۳. شکاف بین مستندات و پیاده‌سازی

✅ هیچ شکاف قابل‌تشخیصی بین مستندات و پیاده‌سازی یافت نشد.

پیاده‌سازی با قواعد R6 (۷ قلم الزامی هر موتور) و API عمومی تعریف‌شده در مستندات همخوان است.

### توصیه‌ها برای نگهداری

- پوشش تست را به‌روز نگه دارید؛ هر بار که متدی به مستندات اضافه می‌شود، تست متناظر را اضافه کنید.
- با هر تغییر API عمومی، نسخهٔ `module.json` را bump کنید.
- در صورت تغییر رفتار، بلوک YAML ماشین‌خوان مستندات را به‌روز کنید.
## ۴. API عمومی مورد انتظار (از مستندات)

| کلاس | متدها |
|------|-------|
| `WorkflowManager` | `define()`, `start()`, `instance()`, `definitions()` |
| `ProcessInstance` | `fire()`, `currentState()`, `can()`, `variables()`, `complete()`, `cancel()` |

## ۵. سناریوهای تست مورد انتظار

| ID | سناریو |
|----|--------|
| `T-01` | تعریف فرایند + validation |
| `T-02` | fire با guard رد → IllegalTransition |
| `T-03` | Exclusive gateway (branch درست) |
| `T-04` | Parallel fork/join (هر دو token) |
| `T-05` | User task + assignee + claim |
| `T-06` | Timer |
| `T-07` | History ثبت |
| `T-08` | مجوز (rbac) روی transition |
| `T-09` | Sub-process |
| `T-10` | JSON definition load |

## ۶. معیارهای پذیرش

`AC-WF-01`, `AC-WF-11`

## ۸. جداول دیتابیس

- `tvn_workflows`
- `tvn_process_instances`
- `tvn_process_tasks`
- `tvn_process_history`

## ۹. نقشه راه تکمیل (Worklog)

✅ نقشه راه اضافی لازم نیست؛ پیاده‌سازی با مستندات همخوان است.

توصیه می‌شود در مراحل بعدی:

1. افزودن benchmarkهای رسمی برای اهداف کارایی مستندشده.
2. تدوین README با مثال‌های کاربردی.
3. افزودن CI workflow برای اجرای خودکار تست‌ها در هر PR.
## ۱۰. منابع

- مستندات رسمی: [Tavaan-Doc/docs/03-engines/](https://github.com/Derogaramin/Tavaan-Doc/tree/main/docs/03-engines)
- رجیستری موتورها: [DOC-030](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/README.md)
- ساختار استاندارد موتور: ۷ قلم الزامی (قاعده R6)

---
*این سند توسط Tavaan Doc-Comparator در 2026-11-03 05:36:38 UTC تولید شده است.*