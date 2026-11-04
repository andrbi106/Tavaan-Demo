# Tavaan Engine — Session

> **نشست** — موتور Session در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.session` |
| نام | `Session` |
| عنوان فارسی | نشست |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Session` |
| کلاس موتور | `Tavaan\Engine\Session\SessionEngine` |
| Facade | Session (`session`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Session;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class SessionEngine extends BaseEngine
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

**فایل:** `src/SessionEngine.php`

## توضیحات

همهٔ درایورها فعال: file/cookie/array/database (M3) + redis (M4 — روی موتور cache با fallback به file).

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `http`, `crypt`, `config`

**موتورهای اختیاری:** `database`, `cache`

## Service Providerها

- `Tavaan\Engine\Session\SessionServiceProvider`

## جداول دیتابیس

- `sessions`

## دستورات CLI

- `session:clear`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Session.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
