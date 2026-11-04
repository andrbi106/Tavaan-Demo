# Tavaan Engine — Event

> **رویداد و Outbox** — موتور Event در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.event` |
| نام | `Event` |
| عنوان فارسی | رویداد و Outbox |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Event` |
| کلاس موتور | `Tavaan\Engine\Event\EventEngine` |
| Facade | Event (`events`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Event;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class EventEngine extends BaseEngine
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

**فایل:** `src/EventEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `container`, `config`

## Service Providerها

- `Tavaan\Engine\Event\EventServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Event.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
