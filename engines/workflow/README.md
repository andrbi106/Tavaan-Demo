# Tavaan Engine — Workflow

> **گردش‌کار** — موتور Workflow در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.workflow` |
| نام | `Workflow` |
| عنوان فارسی | گردش‌کار |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Workflow` |
| کلاس موتور | `Tavaan\Engine\Workflow\WorkflowEngine` |
| Facade | Workflow (`workflow`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Workflow;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class WorkflowEngine extends BaseEngine
{
    public function __construct(EngineManifestInterface $manifest)
    {
        parent::__construct($manifest);
    }

    public function register(ContainerInterface $container): void
    {
        parent::register($container);
        // bindingهای اختصاصی موتور
    }

    public function boot(): void
    {
        parent::boot();
    }
}
```

**فایل:** `src/WorkflowEngine.php`

## توضیحات

M7 — State Machine/BPM خودنوشته: DSL تعریف فرایند (ProcessBuilder) + JSON/Schema (Designer)، Guard/Action، دروازهٔ انحصاری (اولین guard موفق) و موازی (fork/join با Token)، Human Task (assignee role:/user: + claim/complete/delegate)، Timer (انسانی/ISO-8601 + workflow:timers)، Sub-Process (پیش‌برد والد با تکمیل فرزند)، History، Visualizer (DOT/Mermaid)، fire اتمیک (snapshot/restore)، محافظ DoS (max_active_instances) و mask متغیرهای حساس.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

**موتورهای اختیاری:** `event`, `database`, `migration`, `rbac`, `queue`, `console`, `auth`

## Service Providerها

- `Tavaan\Engine\Workflow\WorkflowManagerServiceProvider`

## جداول دیتابیس

- `tvn_workflows`
- `tvn_process_instances`
- `tvn_process_tasks`
- `tvn_process_history`

## دستورات CLI

- `workflow:timers`
- `workflow:list`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Workflow.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
