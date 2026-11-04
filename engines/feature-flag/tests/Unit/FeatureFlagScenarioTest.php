<?php

declare(strict_types=1);

namespace Tavaan\Engine\FeatureFlag\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\FeatureFlag\FeatureManager;

/**
 * T-NN scenario tests for the feature-flag engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class FeatureFlagScenarioTest extends TestCase
{
    public function test_t01_enabled_flag_returns_true(): void
    {
        // Scenario T-01
$m = new FeatureManager(["flags" => ["beta" => ["enabled" => true, "percentage" => 100]]]);
        $this->assertTrue($m->enabled("beta"));
    }
    public function test_t02_disabled_flag_returns_false(): void
    {
        // Scenario T-02
$m = new FeatureManager(["flags" => ["alpha" => ["enabled" => false, "percentage" => 100]]]);
        $this->assertFalse($m->enabled("alpha"));
    }
    public function test_t03_unknown_flag_returns_false(): void
    {
        // Scenario T-03
$m = new FeatureManager([]);
        $this->assertFalse($m->enabled("nonexistent"));
    }
    public function test_t04_value_returns_default_if_disabled(): void
    {
        // Scenario T-04
$m = new FeatureManager(["flags" => ["x" => ["enabled" => false, "value" => "v1"]]]);
        $this->assertSame("default", $m->value("x", "default"));
    }
    public function test_t05_define_registers_flag_at_runtime(): void
    {
        // Scenario T-05
$m = new FeatureManager([]);
        $m->define("new", ["enabled" => true, "value" => 42]);
        $this->assertTrue($m->enabled("new"));
        $this->assertSame(42, $m->value("new", 0));
    }
    public function test_t06_kill_disables_flag(): void
    {
        // Scenario T-06
$m = new FeatureManager(["flags" => ["k" => ["enabled" => true, "percentage" => 100]]]);
        $m->kill("k");
        $this->assertFalse($m->enabled("k"));
    }
    public function test_t07_revive_re_enables_flag(): void
    {
        // Scenario T-07
$m = new FeatureManager(["flags" => ["k" => ["enabled" => true, "percentage" => 100]]]);
        $m->kill("k");
        $this->assertFalse($m->enabled("k"));
        $m->revive("k");
        $this->assertTrue($m->enabled("k"));
    }
    public function test_t08_experiment_picks_consistent_variant_for_actor(): void
    {
        // Scenario T-08
$m = new FeatureManager([]);
        $a1 = $m->experiment("ab", ["A", "B"], "user-1");
        $a2 = $m->experiment("ab", ["A", "B"], "user-1");
        $this->assertSame($a1, $a2, "same actor must get same variant");
        // Different actor may or may not get same variant, but at least one of 100 actors hits each
        $variants = ["A" => 0, "B" => 0];
        for ($i = 1; $i <= 100; $i++) {
            $v = $m->experiment("ab", ["A", "B"], "user-$i");
            $variants[$v] = ($variants[$v] ?? 0) + 1;
        }
        $this->assertGreaterThan(0, $variants["A"]);
        $this->assertGreaterThan(0, $variants["B"]);
    }
}
