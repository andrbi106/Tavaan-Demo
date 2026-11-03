# Upgrade

> ارتقای امن نسخه فریمورک با Data Patch، Backup، Rollback و Maintenance Mode.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.upgrade` |
| DOC-ID | `DOC-081E` |
| Namespace | `Tavaan\Engine\Upgrade` |
| اولویت | P2 (فاز M8) |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-Upgrade |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/upgrade.md

## استفاده

```php
use Tavaan\Engine\Upgrade\UpgradeManager;
use Tavaan\Engine\Upgrade\UpgradeStep;
use Tavaan\Engine\Upgrade\DataPatch;

$mgr = new UpgradeManager();
$mgr->setCurrentVersion('1.0.0');

// افزودن گام‌های ارتقا
$mgr->addStep(new UpgradeStep('1.0.1', 'add column', up: fn() => $db->exec('ALTER TABLE users ADD COLUMN phone TEXT')));
$mgr->addStep(new UpgradeStep('1.1.0', 'data backfill', up: fn() => $db->exec('UPDATE users SET status = "active"'));

// ارتقا
$result = $mgr->upgrade('1.1.0');
// ['success' => true, 'steps_run' => 2, 'version' => '1.1.0']

// Rollback در صورت نیاز
$mgr->rollback();

// بررسی گام‌های pending
$pending = $mgr->pendingSteps('1.1.0');
```

## مستندات

[docs/03-engines/upgrade.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/upgrade.md)

---
*Generated on 2026-11-03.*
