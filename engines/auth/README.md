# Tavaan Engine — Auth

> **احراز هویت** — موتور Auth در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.auth` |
| نام | `Auth` |
| عنوان فارسی | احراز هویت |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Auth` |
| کلاس موتور | `Tavaan\Engine\Auth\AuthEngine` |
| Facade | Auth (`auth`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Auth;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class AuthEngine extends BaseEngine
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

**فایل:** `src/AuthEngine.php`

## توضیحات

M4 — چند Guard (session/jwt)، JWT خودنوشته با algorithm pinning، refresh rotation + reuse detection، OTP یک‌بارمصرف، TOTP RFC 6238، Device tracking، Session hardening، Password broker.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `session`, `hash`, `crypt`, `event`, `database`, `cache`, `config`

**موتورهای اختیاری:** `http`, `orm`, `notification`

## Service Providerها

- `Tavaan\Engine\Auth\AuthManagerServiceProvider`

## جداول دیتابیس

- `password_resets`
- `personal_access_tokens`
- `user_devices`
- `two_factor_secrets`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Auth.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
