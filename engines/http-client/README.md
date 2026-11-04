# Tavaan Engine — HttpClient

> **کلاینت HTTP** — موتور HttpClient در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.http-client` |
| نام | `HttpClient` |
| عنوان فارسی | کلاینت HTTP |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\HttpClient` |
| کلاس موتور | `Tavaan\Engine\HttpClient\HttpClientEngine` |
| Facade | Http (`http`) |

## کلاس موتور

```php
namespace Tavaan\Engine\HttpClient;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class HttpClientEngine extends BaseEngine
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

**فایل:** `src/HttpClientEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `event`

## Service Providerها

- `Tavaan\Engine\HttpClient\HttpClientServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Http-Client.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
