<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Container\Container;

/**
 * T-NN scenario tests for the container engine (real implementations).
 */
final class ContainerScenarioTest extends TestCase
{
    public function test_t01_singleton_returns_same_instance(): void
    {
        // Scenario T-01
$c = new Container();
        $c->singleton("x", fn() => new \stdClass());
        $a = $c->make("x");
        $b = $c->make("x");
        $this->assertSame($a, $b);
    }

    public function test_t02_transient_returns_new_instance(): void
    {
        // Scenario T-02
$c = new Container();
        $c->bind("y", fn() => new \stdClass());
        $a = $c->make("y");
        $b = $c->make("y");
        $this->assertNotSame($a, $b);
    }

    public function test_t08_tagged_lazy_iteration(): void
    {
        // Scenario T-08
$c = new Container();
        $c->bind("a", fn() => "A");
        $c->bind("b", fn() => "B");
        $c->tag(["a", "b"], "grp");
        $tagged = $c->tagged("grp");
        $values = [];
        foreach ($tagged as $item) { $values[] = $item; }
        $this->assertContains("A", $values);
        $this->assertContains("B", $values);
    }
}
