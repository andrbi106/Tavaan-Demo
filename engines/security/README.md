# Tavaan Engine — Security

> **امنیت عرضی (CSRF/RateLimit/Sanitizer/SSRF)** — موتور Security در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.security` |
| نام | `Security` |
| عنوان فارسی | امنیت عرضی (CSRF/RateLimit/Sanitizer/SSRF) |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Security` |
| کلاس موتور | `Tavaan\Engine\Security\SecurityEngine` |
| Facade | Security (`security`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Security;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class SecurityEngine extends BaseEngine
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

**فایل:** `src/SecurityEngine.php`

## توضیحات

M4 — CSRF (double-submit)، RateLimit (sliding/fixed/token-bucket)، Security Headers، HTML Sanitizer خالص (allowlist)، SSRF Guard با DNS pinning، Input Normalizer، Signature.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `http`, `crypt`, `cache`, `config`

**موتورهای اختیاری:** `session`, `router`

## Service Providerها

- `Tavaan\Engine\Security\SecurityServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Security.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
