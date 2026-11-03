<?php

declare(strict_types=1);

namespace Tavaan\Engine\Upgrade\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Upgrade\UpgradeManager;
use Tavaan\Engine\Upgrade\UpgradeStep;
use Tavaan\Engine\Upgrade\UpgradeServiceProvider;

final class UpgradeFeatureTest extends TestCase
{
    public function test_end_to_end_upgrade_workflow(): void
    {
        $provider = new UpgradeServiceProvider([]);
        $provider->register();
        $m = $provider->getManager();
        $m->setCurrentVersion('1.0.0');
        $m->addStep(new UpgradeStep('1.0.1', 'patch 1', up: fn() => null));
        $m->addStep(new UpgradeStep('1.1.0', 'patch 2', up: fn() => null));
        $result = $m->upgrade('1.1.0');
        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['steps_run']);
        $this->assertSame('1.1.0', $result['version']);
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/upgrade.php';
        $this->assertSame('storage/backups', $config['backup_dir']);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/upgrade.php';
        $this->assertArrayHasKey('error_upgrade', $m);
    }
}
