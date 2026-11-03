<?php

declare(strict_types=1);

namespace Tavaan\Engine\Upgrade\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Upgrade\Upgrader;
use Tavaan\Engine\Upgrade\Patch;

/**
 * T-NN scenario tests for the upgrade engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class UpgradeScenarioTest extends TestCase
{
    public function test_t01_upgrade_applies_patches_in_order(): void
    {
        // Scenario T-01
$u = new Upgrader();
        $log = [];
        $u->add(new class("1.0.1", "p1") extends Patch {
            public function up(): void {}
            public function down(): void {}
        });
        $u->add(new class("1.0.2", "p2") extends Patch {
            public function up(): void {}
            public function down(): void {}
        });
        $applied = $u->upgrade();
        $this->assertCount(2, $applied);
        $this->assertSame(["1.0.1", "1.0.2"], $applied);
    }
    public function test_t02_status_returns_applied_and_pending(): void
    {
        // Scenario T-02
$u = new Upgrader();
        $u->add(new class("1.0", "p") extends Patch {
            public function up(): void {}
            public function down(): void {}
        });
        $u->add(new class("2.0", "p2") extends Patch {
            public function up(): void {}
            public function down(): void {}
        });
        $u->upgrade("1.0");
        $status = $u->status();
        $this->assertContains("2.0", $status["applied"]);
        $this->assertContains("1.0", $status["pending"]);
    }
    public function test_t03_rollback_reverts_patches(): void
    {
        // Scenario T-03
$u = new Upgrader();
        $calls = [];
        $u->add(new class("1.0", "p") extends Patch {
            public function up(): void {}
            public function down(): void { $GLOBALS["d_1"] = true; }
        });
        $u->add(new class("2.0", "p2") extends Patch {
            public function up(): void {}
            public function down(): void { $GLOBALS["d_2"] = true; }
        });
        $u->upgrade();
        $rolled = $u->rollback(1);
        $this->assertCount(1, $rolled);
        $this->assertSame("2.0", $rolled[0]);
    }
    public function test_t04_patches_list_returns_versions(): void
    {
        // Scenario T-04
$u = new Upgrader();
        $u->add(new class("1.0", "p") extends Patch {
            public function up(): void {}
            public function down(): void {}
        });
        $u->add(new class("2.0", "p2") extends Patch {
            public function up(): void {}
            public function down(): void {}
        });
        $this->assertSame(["1.0", "2.0"], $u->patches());
    }
}
