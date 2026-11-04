# Tavaan Engine — API

> **لایهٔ API** — موتور API در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.api` |
| نام | `API` |
| عنوان فارسی | لایهٔ API |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Api` |
| کلاس موتور | `Tavaan\Engine\Api\APIEngine` |

## کلاس موتور

```php
namespace Tavaan\Engine\Api;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class APIEngine extends BaseEngine
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

**فایل:** `src/APIEngine.php`

## توضیحات

M6 — JsonResource با when/whenLoaded/whenHas/whenCounted/mergeWhen/whenPivotLoaded، AnonymousResourceCollection با meta/links صفحه‌بندی، Versioning (url/header/query + هدرهای Deprecation/Sunset)، Idempotency-Key با قفل in-progress و RFC 9457، Throttle (نام‌دار و عددی، per-user/key/IP، هدرهای X-RateLimit-*)، OpenAPI 3.1 generator خودنوشته (attribute-based + FormRequest rules + YAML emitter داخلی).

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `http`

**موتورهای اختیاری:** `validation`, `orm`, `cache`, `router`

## Service Providerها

- `Tavaan\Engine\Api\JsonResourceServiceProvider`

## دستورات CLI

- `api:docs`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-API.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
