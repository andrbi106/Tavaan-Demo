# Tavaan Engine — Router

> **مسیریاب Compiled Trie** — موتور Router در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.router` |
| نام | `Router` |
| عنوان فارسی | مسیریاب Compiled Trie |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Http\Routing` |
| کلاس موتور | `Tavaan\Engine\Http\Routing\RouterEngine` |
| Facade | Route (`router`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Http\Routing;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class RouterEngine extends BaseEngine
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

**فایل:** `src/RouterEngine.php`

## توضیحات

کد در engines/http/src/Routing (namespace مطابق DOC-040 machine block).

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `http`, `container`, `config`

## Service Providerها

- `Tavaan\Engine\Http\Routing\RouterServiceProvider`

## دستورات CLI

- `route:list`
- `route:cache`
- `route:clear`
- `route:match`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Router.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
