# Tavaan Engine — Backup

> **پشتیبان‌گیری** — موتور Backup در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.backup` |
| نام | `Backup` |
| عنوان فارسی | پشتیبان‌گیری |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Backup` |
| کلاس موتور | `Tavaan\Engine\Backup\BackupEngine` |
| Facade | Backup (`backup`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Backup;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class BackupEngine extends BaseEngine
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

**فایل:** `src/BackupEngine.php`

## توضیحات

M5 — DB dump خالص (بدون mysqldump، chunked با cursor → حافظهٔ ثابت)، ZIP خالص (ZipWriterPure + ZipReader خودنوشته)، 🔒 رمزنگاری AES-256-GCM با کلید جدا از APP_KEY، verify (hash+decrypt)، restore با --force + maintenance mode، retention/prune.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `filesystem`

**موتورهای اختیاری:** `database`, `crypt`, `cache`, `compat`

## Service Providerها

- `Tavaan\Engine\Backup\BackupManagerServiceProvider`

## دستورات CLI

- `backup:run`
- `backup:list`
- `backup:verify`
- `backup:restore`
- `backup:prune`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Backup.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
