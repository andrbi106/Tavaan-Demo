# Tavaan Engine — Hash

> **هش رمز عبور** — موتور Hash در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.hash` |
| نام | `Hash` |
| عنوان فارسی | هش رمز عبور |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Hash` |
| کلاس موتور | `Tavaan\Engine\Hash\HashEngine` |
| Facade | Hash (`hash`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Hash;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class HashEngine extends BaseEngine
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

**فایل:** `src/HashEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

## Service Providerها

- `Tavaan\Engine\Hash\HashServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Hash.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
