<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Config\Repository;

/**
 * T-NN scenario tests for the config engine (real implementations).
 */
final class ConfigScenarioTest extends TestCase
{
    public function test_t01_dot_get_multiple_levels(): void
    {
        // Scenario T-01
$repo = new Repository(["app" => ["name" => "Tavaan", "version" => ["major" => 1, "minor" => 0]]]);
        $this->assertSame("Tavaan", $repo->get("app.name"));
        $this->assertSame(1, $repo->get("app.version.major"));
    }

    public function test_t02_set_creates_intermediate_paths(): void
    {
        // Scenario T-02
$repo = new Repository([]);
        $repo->set("a.b.c", "value");
        $this->assertSame("value", $repo->get("a.b.c"));
    }

    public function test_t10_masking_in_to_json(): void
    {
        // Scenario T-10
$repo = new Repository(["db" => ["password" => "secret123"]]);
        $masked = $repo->masked();
        $this->assertNotSame("secret123", $masked["db"]["password"]);
    }
}
