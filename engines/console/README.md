# Tavaan Engine — Console

> **چارچوب CLI** — موتور Console در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.console` |
| نام | `Console` |
| عنوان فارسی | چارچوب CLI |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Console` |
| کلاس موتور | `Tavaan\Engine\Console\ConsoleEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\Console;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ConsoleEngine extends BaseEngine
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

**فایل:** `src/ConsoleEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `container`, `config`

## Service Providerها

- `Tavaan\Engine\Console\ConsoleServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Console.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
