# Tavaan Engine — RBAC

> **نقش و کنترل دسترسی** — موتور RBAC در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.rbac` |
| نام | `RBAC` |
| عنوان فارسی | نقش و کنترل دسترسی |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Rbac` |
| کلاس موتور | `Tavaan\Engine\Rbac\RBACEngine` |
| Facade | Gate (`gate`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Rbac;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class RBACEngine extends BaseEngine
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

**فایل:** `src/RBACEngine.php`

## توضیحات

M4 — RBAC+ABAC با Gate/Policy، سلسله‌مراتب نقش، wildcard، delegation موقت، Row-Level Scope، sync از manifest ماژول‌ها.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `auth`, `database`, `cache`, `event`, `config`

## Service Providerها

- `Tavaan\Engine\Rbac\GateServiceProvider`

## جداول دیتابیس

- `tvn_permissions`
- `tvn_roles`
- `tvn_role_user`
- `tvn_permission_role`
- `tvn_delegations`

## دستورات CLI

- `rbac:sync`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-RBAC.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
