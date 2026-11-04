<?php

declare(strict_types=1);

namespace Tavaan\Engine\Plugin\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Plugin\Plugin;
use Tavaan\Engine\Plugin\PluginManager;
use Tavaan\Engine\Plugin\Hook;

/**
 * T-NN scenario tests for the plugin engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class PluginScenarioTest extends TestCase
{
    public function test_t01_register_and_get(): void
    {
        // Scenario T-01
$pm = new PluginManager();
        $p = new Plugin("p1", "Plugin 1", "1.0.0", "/tmp", true, []);
        $pm->register($p);
        $this->assertTrue($pm->has("p1"));
        $this->assertSame($p, $pm->get("p1"));
    }
    public function test_t02_enabled_filter(): void
    {
        // Scenario T-02
$pm = new PluginManager();
        $pm->register(new Plugin("on", "On", "1.0", "/tmp", true, []));
        $pm->register(new Plugin("off", "Off", "1.0", "/tmp", false, []));
        $enabled = $pm->enabled();
        $this->assertCount(1, $enabled);
        $this->assertSame("on", $enabled[0]->id);
    }
    public function test_t03_hook_listen_and_fire(): void
    {
        // Scenario T-03
$h = new Hook();
        $h->listen("filter", fn($v) => $v * 2);
        $out = $h->fire("filter", 5);
        $this->assertSame(10, $out);
    }
    public function test_t04_hook_priority_order(): void
    {
        // Scenario T-04
$h = new Hook();
        $h->listen("f", fn($v) => $v . "1", 20);
        $h->listen("f", fn($v) => $v . "2", 10);
        $out = $h->fire("f", "X");
        // Priority 10 (lower) fires first
        $this->assertSame("X21", $out);
    }
    public function test_t05_hook_remove(): void
    {
        // Scenario T-05
$h = new Hook();
        $cb = fn($v) => $v + 1;
        $h->listen("inc", $cb);
        $this->assertTrue($h->has("inc"));
        $h->remove("inc");
        $this->assertFalse($h->has("inc"));
    }
}
