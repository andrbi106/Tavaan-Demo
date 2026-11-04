# Tavaan Engine — CORS

> **مدیریت CORS** — موتور CORS در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.cors` |
| نام | `CORS` |
| عنوان فارسی | مدیریت CORS |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Http\Cors` |
| کلاس موتور | `Tavaan\Engine\Http\Cors\CORSEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\Http\Cors;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class CORSEngine extends BaseEngine
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

**فایل:** `src/CORSEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `http`, `config`

## Service Providerها

- `Tavaan\Engine\Http\Cors\CorsServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-CORS.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
