# Tavaan Engine — Scaffold

> **تولید کد (make:*)** — موتور Scaffold در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.scaffold` |
| نام | `Scaffold` |
| عنوان فارسی | تولید کد (make:*) |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Scaffold` |
| کلاس موتور | `Tavaan\Engine\Scaffold\ScaffoldEngine` |
| Facade |  |

## کلاس موتور

```php
namespace Tavaan\Engine\Scaffold;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class ScaffoldEngine extends BaseEngine
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

**فایل:** `src/ScaffoldEngine.php`

## توضیحات

M8/TVN-0077: Stub engine خودنوشته ({{var}}/{{#if}}/{{#unless}}/{{#each}})، ۱۷ Generator، idempotent با --force + backup .bak، sandbox مسیر (path traversal → رد).

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `console`, `filesystem`

## Service Providerها

- `Tavaan\Engine\Scaffold\GeneratorManagerServiceProvider`

## دستورات CLI

- `make:module`
- `make:controller`
- `make:model`
- `make:migration`
- `make:request`
- `make:policy`
- `make:command`
- `make:job`
- `make:event`
- `make:listener`
- `make:seeder`
- `make:factory`
- `make:test`
- `make:widget`
- `make:resource`
- `make:engine`
- `make:middleware`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Scaffold.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
