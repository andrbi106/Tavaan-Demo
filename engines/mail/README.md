# Tavaan Engine — Mail

> **پست الکترونیک** — موتور Mail در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.mail` |
| نام | `Mail` |
| عنوان فارسی | پست الکترونیک |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Mail` |
| کلاس موتور | `Tavaan\Engine\Mail\MailEngine` |
| Facade | Mail (`mailer`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Mail;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class MailEngine extends BaseEngine
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

**فایل:** `src/MailEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `event`

## Service Providerها

- `Tavaan\Engine\Mail\MailServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Mail.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
