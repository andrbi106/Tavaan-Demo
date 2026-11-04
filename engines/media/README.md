# Tavaan Engine — Media

> **آپلود و پردازش رسانه** — موتور Media در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.media` |
| نام | `Media` |
| عنوان فارسی | آپلود و پردازش رسانه |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Media` |
| کلاس موتور | `Tavaan\Engine\Media\MediaEngine` |
| Facade | Media (`media`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Media;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class MediaEngine extends BaseEngine
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

**فایل:** `src/MediaEngine.php`

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

## Service Providerها

- `Tavaan\Engine\Media\MediaServiceProvider`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Media.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
