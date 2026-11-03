<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Migration\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Migration\Migrator;
use Tavaan\Engine\Database\Migration\Migration;

/**
 * T-NN scenario tests for the migration engine (real implementations).
 */
final class MigrationScenarioTest extends TestCase
{
    public function test_t01_migrator_run(): void
    {
        // Scenario T-01
$m = new Migrator();
        $mig = new class("1") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig->version = "1";
        $m->getRepository()->add($mig);
        $executed = $m->run();
        $this->assertCount(1, $executed);
        $this->assertSame("1", $executed[0]);
    }

    public function test_t02_migrator_status(): void
    {
        // Scenario T-02
$m = new Migrator();
        $mig = new class("1") extends Migration {
            public function up(): void {}
            public function down(): void {}
        };
        $mig->version = "1";
        $m->getRepository()->add($mig);
        $m->run();
        $status = $m->status();
        $this->assertCount(1, $status["ran"]);
    }
}
