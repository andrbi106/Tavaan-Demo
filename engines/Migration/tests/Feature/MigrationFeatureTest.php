<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Migration\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Migration\Migrator;
use Tavaan\Engine\Database\Migration\Migration;

/**
 * Feature tests for the migration engine.
 */
final class MigrationFeatureTest extends TestCase
{
    public function test_migrator_run_executes_pending(): void
    {
$m = new Migrator();
        $mig = new class("2026_01_01") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig->version = "2026_01_01";
        $m->getRepository()->add($mig);
        $executed = $m->run();
        $this->assertCount(1, $executed);
        $this->assertSame("2026_01_01", $executed[0]);
    }

    public function test_migrator_rollback_reverses_last(): void
    {
$m = new Migrator();
        $mig1 = new class("1") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig1->version = "1";
        $mig2 = new class("2") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig2->version = "2";
        $m->getRepository()->add($mig1);
        $m->getRepository()->add($mig2);
        $m->run();
        $rolled = $m->rollback(1);
        $this->assertCount(1, $rolled);
        $this->assertSame("2", $rolled[0]);
    }

    public function test_migrator_status_shows_ran_and_pending(): void
    {
$m = new Migrator();
        $mig1 = new class("1") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig1->version = "1";
        $mig2 = new class("2") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig2->version = "2";
        $m->getRepository()->add($mig1);
        $m->getRepository()->add($mig2);
        $m->run();
        $status = $m->status();
        $this->assertCount(2, $status["ran"]);
        $this->assertCount(0, $status["pending"]);
    }

    public function test_migrator_reset_rolls_back_all(): void
    {
$m = new Migrator();
        $mig1 = new class("1") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig1->version = "1";
        $mig2 = new class("2") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig2->version = "2";
        $m->getRepository()->add($mig1);
        $m->getRepository()->add($mig2);
        $m->run();
        $rolled = $m->reset();
        $this->assertCount(2, $rolled);
    }
}
