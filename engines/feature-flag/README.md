# Tavaan Engine — FeatureFlag

> **پرچم قابلیت** — موتور FeatureFlag در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.feature-flag` |
| نام | `FeatureFlag` |
| عنوان فارسی | پرچم قابلیت |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\FeatureFlag` |
| کلاس موتور | `Tavaan\Engine\FeatureFlag\FeatureFlagEngine` |
| Facade | Feature (`feature`) |

## کلاس موتور

```php
namespace Tavaan\Engine\FeatureFlag;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class FeatureFlagEngine extends BaseEngine
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

**فایل:** `src/FeatureFlagEngine.php`

## توضیحات

M6 — Targeting با قواعد role/permission/user_id/tenant/percentage/env/ip، Kill Switch فوری (cache)، درصد پایدار (crc32(seed|feature))، A/B Testing پایدار، درایورهای config/database با کش نسخه‌دار، Feature::for($user) scoped.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

**موتورهای اختیاری:** `cache`, `database`, `rbac`, `tenancy`

## Service Providerها

- `Tavaan\Engine\FeatureFlag\FeatureManagerServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Feature-Flag.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
