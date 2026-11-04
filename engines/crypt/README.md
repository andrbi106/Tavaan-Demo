# Tavaan Engine — Crypt

> **رمزنگاری** — موتور Crypt در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.crypt` |
| نام | `Crypt` |
| عنوان فارسی | رمزنگاری |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Crypt` |
| کلاس موتور | `Tavaan\Engine\Crypt\CryptEngine` |
| Facade | Crypt (`encrypter`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Crypt;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class CryptEngine extends BaseEngine
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

**فایل:** `src/CryptEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

## Service Providerها

- `Tavaan\Engine\Crypt\CryptServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Crypt.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
