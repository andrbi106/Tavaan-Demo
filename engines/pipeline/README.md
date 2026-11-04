# Tavaan Engine — Pipeline

> **خط لوله** — موتور Pipeline در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.pipeline` |
| نام | `Pipeline` |
| عنوان فارسی | خط لوله |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Pipeline` |
| کلاس موتور | `Tavaan\Engine\Pipeline\PipelineEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\Pipeline;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class PipelineEngine extends BaseEngine
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

**فایل:** `src/PipelineEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `container`

## Service Providerها

- `Tavaan\Engine\Pipeline\PipelineServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Pipeline.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
