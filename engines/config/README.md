# Tavaan Engine — Config

> **پیکربندی و محیط** — موتور Config در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.config` |
| نام | `Config` |
| عنوان فارسی | پیکربندی و محیط |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Config` |
| کلاس موتور | `Tavaan\Engine\Config\ConfigEngine` |
| Facade | Config (`config`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Config;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ConfigEngine extends BaseEngine
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

**فایل:** `src/ConfigEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `container`

## Service Providerها

- `Tavaan\Engine\Config\ConfigServiceProvider`

## دستورات CLI

- `config:cache`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Config.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
