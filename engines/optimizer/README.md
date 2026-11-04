# Tavaan Engine — Optimizer

> **بهینه‌سازی** — موتور Optimizer در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.optimizer` |
| نام | `Optimizer` |
| عنوان فارسی | بهینه‌سازی |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Optimizer` |
| کلاس موتور | `Tavaan\Engine\Optimizer\OptimizerEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\Optimizer;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class OptimizerEngine extends BaseEngine
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

**فایل:** `src/OptimizerEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `console`

## Service Providerها

- `Tavaan\Engine\Optimizer\OptimizerServiceProvider`

## دستورات CLI

- `optimize`
- `optimize:clear`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Optimizer.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
