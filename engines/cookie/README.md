# Tavaan Engine — Cookie

> **کوکی رمزنگاری‌شده** — موتور Cookie در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.cookie` |
| نام | `Cookie` |
| عنوان فارسی | کوکی رمزنگاری‌شده |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Http\Cookie` |
| کلاس موتور | `Tavaan\Engine\Http\Cookie\CookieEngine` |
| Facade | Cookie (`cookie`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Http\Cookie;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class CookieEngine extends BaseEngine
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

**فایل:** `src/CookieEngine.php`

## توضیحات

کد در engines/http/src/Cookie (namespace مطابق DOC-042).

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `http`, `crypt`

## Service Providerها

- `Tavaan\Engine\Http\Cookie\CookieJarServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Cookie.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
