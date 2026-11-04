# Tavaan Engine — Plugin

> **افزونهٔ ثالث (sandbox + امضا)** — موتور Plugin در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.plugin` |
| نام | `Plugin` |
| عنوان فارسی | افزونهٔ ثالث (sandbox + امضا) |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Plugin` |
| کلاس موتور | `Tavaan\Engine\Plugin\PluginEngine` |
| Facade | Plugin (`plugins`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Plugin;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class PluginEngine extends BaseEngine
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

**فایل:** `src/PluginEngine.php`

## توضیحات

M8/TVN-0079: manifest + Ed25519 (sodium) + plugins.lock (SHA-256) + scanner توکنی + sandbox (autoload append/vendor ایزوله/capabilities) + Hook (اعلام‌شده‌محور + timeout/memory) + چرخهٔ عمر کامل.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `module`, `filesystem`, `crypt`

## Service Providerها

- `Tavaan\Engine\Plugin\PluginManagerServiceProvider`

## دستورات CLI

- `plugin:install`
- `plugin:enable`
- `plugin:disable`
- `plugin:update`
- `plugin:uninstall`
- `plugin:list`
- `plugin:verify`
- `plugin:scan`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Plugin.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
