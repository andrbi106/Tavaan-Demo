<?php

declare(strict_types=1);

namespace Tavaan\Engine\Upgrade\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Upgrade\UpgradeManager;
use Tavaan\Engine\Upgrade\UpgradeStep;
use Tavaan\Engine\Upgrade\DataPatch;
use Tavaan\Engine\Upgrade\UpgradeServiceProvider;

final class UpgradeTest extends TestCase
{
    public function test_data_patch_abstract(): void
    {
        $patch = new class extends DataPatch {
            public string $version = '1.0.1';
            public string $description = 'test patch';
            public bool $upCalled = false;
            public function up(): void { $this->upCalled = true; }
        };
        $this->assertSame('1.0.1', $patch->version);
        $patch->up();
        $this->assertTrue($patch->upCalled);
    }

    public function test_upgrade_step_construct(): void
    {
        $up = fn() => null;
        $down = fn() => null;
        $step = new UpgradeStep('1.0.1', 'add column', $up, $down);
        $this->assertSame('1.0.1', $step->version);
        $this->assertSame('add column', $step->name);
    }

    public function test_manager_add_step(): void
    {
        $m = new UpgradeManager([]);
        $m->addStep(new UpgradeStep('1.0.1', 'test'));
        $this->assertCount(1, $m->allSteps());
    }

    public function test_manager_set_get_current_version(): void
    {
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $this->assertSame('1.0.0', $m->getCurrentVersion());
    }

    public function test_manager_precheck(): void
    {
        $m = new UpgradeManager([]);
        $this->assertTrue($m->preCheck());  // PHP 8.2+ should pass
    }

    public function test_manager_backup(): void
    {
        $m = new UpgradeManager([]);
        $this->assertFalse($m->isBackupCreated());
        $m->backup();
        $this->assertTrue($m->isBackupCreated());
    }

    public function test_manager_maintenance_mode(): void
    {
        $m = new UpgradeManager([]);
        $this->assertFalse($m->isMaintenanceEnabled());
        $m->enableMaintenance();
        $this->assertTrue($m->isMaintenanceEnabled());
        $m->disableMaintenance();
        $this->assertFalse($m->isMaintenanceEnabled());
    }

    public function test_manager_pending_steps(): void
    {
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $m->addStep(new UpgradeStep('1.0.1', 'step1'));
        $m->addStep(new UpgradeStep('1.0.2', 'step2'));
        $m->addStep(new UpgradeStep('1.1.0', 'step3'));
        $pending = $m->pendingSteps('1.0.2');
        $this->assertContains('1.0.1', $pending);
        $this->assertContains('1.0.2', $pending);
        $this->assertNotContains('1.1.0', $pending);
    }

    public function test_manager_upgrade_runs_steps(): void
    {
        $called = [];
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $m->addStep(new UpgradeStep('1.0.1', 's1', up: function () use (&$called) { $called[] = '1.0.1'; }));
        $m->addStep(new UpgradeStep('1.1.0', 's2', up: function () use (&$called) { $called[] = '1.1.0'; }));
        $result = $m->upgrade('1.1.0');
        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['steps_run']);
        $this->assertSame('1.1.0', $result['version']);
        $this->assertSame(['1.0.1', '1.1.0'], $called);
    }

    public function test_manager_upgrade_partial_target(): void
    {
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $m->addStep(new UpgradeStep('1.0.1', 's1', up: fn() => null));
        $m->addStep(new UpgradeStep('1.1.0', 's2', up: fn() => null));
        $result = $m->upgrade('1.0.1');
        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['steps_run']);
        $this->assertSame('1.0.1', $result['version']);
    }

    public function test_manager_upgrade_no_steps(): void
    {
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $result = $m->upgrade('1.0.0');
        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['steps_run']);
    }

    public function test_manager_upgrade_rolls_back_on_failure(): void
    {
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $m->addStep(new UpgradeStep('1.0.1', 's1', up: function () { throw new \RuntimeException('fail'); }));
        $result = $m->upgrade('1.0.1');
        $this->assertFalse($result['success']);
        $this->assertSame(0, $result['steps_run']);
        $this->assertSame('1.0.0', $result['version']);
    }

    public function test_manager_rollback_calls_down(): void
    {
        $downCalled = [];
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $m->addStep(new UpgradeStep('1.0.1', 's1', up: fn() => null, down: function () use (&$downCalled) { $downCalled[] = '1.0.1'; }));
        $m->addStep(new UpgradeStep('1.0.2', 's2', up: fn() => null, down: function () use (&$downCalled) { $downCalled[] = '1.0.2'; }));
        $m->upgrade('1.0.2');
        $m->rollback();
        // Should be in reverse order
        $this->assertSame(['1.0.2', '1.0.1'], $downCalled);
    }

    public function test_manager_get_executed_steps(): void
    {
        $m = new UpgradeManager([]);
        $m->setCurrentVersion('1.0.0');
        $m->addStep(new UpgradeStep('1.0.1', 's1', up: fn() => null));
        $m->upgrade('1.0.1');
        $this->assertSame(['1.0.1'], $m->getExecutedSteps());
    }

    public function test_manager_all_steps(): void
    {
        $m = new UpgradeManager([]);
        $m->addStep(new UpgradeStep('1.0', 'a'));
        $m->addStep(new UpgradeStep('1.1', 'b'));
        $this->assertCount(2, $m->allSteps());
    }

    public function test_service_provider(): void
    {
        $provider = new UpgradeServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(UpgradeManager::class, $provider->getManager());
        $this->assertContains(UpgradeManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/upgrade.php';
        $this->assertArrayHasKey('backup_dir', $config);
        $this->assertArrayHasKey('auto_backup', $config);
    }
}
