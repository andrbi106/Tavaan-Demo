<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Event\EventDispatcher;

/**
 * T-NN scenario tests for the event engine (real implementations).
 */
final class EventScenarioTest extends TestCase
{
    public function test_t01_dispatch_listener_called(): void
    {
        // Scenario T-01
$d = new EventDispatcher();
        $hit = 0;
        $hitRef = &$hit;
        $d->listen("test.event", function () use (&$hitRef) { $hitRef++; });
        $d->dispatch("test.event");
        $this->assertSame(1, $hit);
    }

    public function test_t02_dispatch_with_positional_payload(): void
    {
        // Scenario T-02
$d = new EventDispatcher();
        $received = null;
        $d->listen("e", function ($val) use (&$received) { $received = $val; });
        $d->dispatch("e", ["hello"]);
        $this->assertSame("hello", $received);
    }
}
