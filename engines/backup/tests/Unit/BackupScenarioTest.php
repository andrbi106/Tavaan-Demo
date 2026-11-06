<?php

declare(strict_types=1);

namespace Tavaan\Engine\Backup\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Backup\BackupManager;
use Tavaan\Engine\Backup\Backup;

/**
 * T-NN scenario tests for the backup engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class BackupScenarioTest extends TestCase
{
    public function test_t01_create_backup(): void
    {
        // Scenario T-01
$mgr = new BackupManager(["backup_dir" => "/tmp/test_backup"]);
        @mkdir("/tmp/test_backup", 0775, true);
        $b = $mgr->create("test", "full");
        $this->assertInstanceOf(Backup::class, $b);
        $this->assertSame("test", $b->name);
        $this->assertFileExists($b->path);
    }
    public function test_t02_list_backups(): void
    {
        // Scenario T-02
$mgr = new BackupManager(["backup_dir" => "/tmp/test_backup2"]);
        @mkdir("/tmp/test_backup2", 0775, true);
        $mgr->create("a", "full");
        $mgr->create("b", "full");
        $this->assertCount(2, $mgr->list());
    }
    public function test_t03_delete_backup(): void
    {
        // Scenario T-03
$mgr = new BackupManager(["backup_dir" => "/tmp/test_backup3"]);
        @mkdir("/tmp/test_backup3", 0775, true);
        $b = $mgr->create("d", "full");
        $this->assertTrue($mgr->delete($b->path));
        $this->assertFalse(file_exists($b->path));
    }
}
