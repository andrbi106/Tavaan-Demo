# Tavaan Engine — Filesystem

> **سامانه فایل** — موتور Filesystem در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.filesystem` |
| نام | `Filesystem` |
| عنوان فارسی | سامانه فایل |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Filesystem` |
| کلاس موتور | `Tavaan\Engine\Filesystem\FilesystemEngine` |
| Facade | Storage (`filesystem`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Filesystem;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class FilesystemEngine extends BaseEngine
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

**فایل:** `src/FilesystemEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

## Service Providerها

- `Tavaan\Engine\Filesystem\FilesystemServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Filesystem.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
