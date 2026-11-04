# Tavaan Engine — Cache

> **حافظه نهان** — موتور Cache در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.cache` |
| نام | `Cache` |
| عنوان فارسی | حافظه نهان |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Cache` |
| کلاس موتور | `Tavaan\Engine\Cache\CacheEngine` |
| Facade | Cache (`cache`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Cache;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class CacheEngine extends BaseEngine
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

**فایل:** `src/CacheEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `event`

## Service Providerها

- `Tavaan\Engine\Cache\CacheServiceProvider`

## دستورات CLI

- `cache:clear`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Cache.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
