# Tavaan Engine — Upgrade

> **ارتقای نسخه** — موتور Upgrade در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.upgrade` |
| نام | `Upgrade` |
| عنوان فارسی | ارتقای نسخه |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Upgrade` |
| کلاس موتور | `Tavaan\Engine\Upgrade\UpgradeEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\Upgrade;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class UpgradeEngine extends BaseEngine
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

**فایل:** `src/UpgradeEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `console`, `database`, `filesystem`

## Service Providerها

- `Tavaan\Engine\Upgrade\UpgradeServiceProvider`

## دستورات CLI

- `upgrade`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Upgrade.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
