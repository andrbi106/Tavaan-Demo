<?php

declare(strict_types=1);

namespace Tavaan\Engine\Scaffold\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Scaffold\GeneratorManager;

/**
 * T-NN scenario tests for the scaffold engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class ScaffoldScenarioTest extends TestCase
{
    public function test_t01_register_generator(): void
    {
        // Scenario T-01
$mgr = new GeneratorManager();
        $mgr->register("model", fn($opts) => ["file1.php"]);
        $this->assertTrue($mgr->has("model"));
    }
    public function test_t02_generate_invokes_callback(): void
    {
        // Scenario T-02
$mgr = new GeneratorManager();
        $mgr->register("controller", fn($opts) => ["c.php"]);
        $result = $mgr->generate("controller", ["name" => "Post"]);
        $this->assertNotNull($result);
        $this->assertSame("controller", $result["generator"]);
        $this->assertSame(["c.php"], $result["files"]);
    }
    public function test_t03_unknown_generator_returns_null(): void
    {
        // Scenario T-03
$mgr = new GeneratorManager();
        $this->assertNull($mgr->generate("unknown"));
    }
}
