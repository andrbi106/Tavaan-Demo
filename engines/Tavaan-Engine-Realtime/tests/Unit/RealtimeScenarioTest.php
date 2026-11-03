<?php

declare(strict_types=1);

namespace Tavaan\Engine\Realtime\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Realtime\RealtimeManager;
use Tavaan\Engine\Realtime\Broadcast;

/**
 * T-NN scenario tests for the realtime engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class RealtimeScenarioTest extends TestCase
{
    public function test_t01_subscribe_broadcast(): void
    {
        // Scenario T-01
$mgr = new RealtimeManager();
        $received = [];
        $mgr->subscribe("chan1", "conn1", function ($event, $data) use (&$received) {
            $received[] = ["event" => $event, "data" => $data];
        });
        $mgr->broadcast("chan1", "ping", ["v" => 1]);
        $this->assertCount(1, $received);
        $this->assertSame("ping", $received[0]["event"]);
    }
    public function test_t02_unsubscribe_removes_callback(): void
    {
        // Scenario T-02
$mgr = new RealtimeManager();
        $hits = 0;
        $cb = function () use (&$hits) { $hits++; };
        $mgr->subscribe("c", "x", $cb);
        $mgr->broadcast("c", "e", null);
        $mgr->unsubscribe("c", "x");
        $mgr->broadcast("c", "e", null);
        $this->assertSame(1, $hits);
    }
    public function test_t03_broadcast_facade_returns_manager(): void
    {
        // Scenario T-03
$b = new Broadcast();
        $mgr = $b->connection();
        $this->assertInstanceOf(RealtimeManager::class, $mgr);
    }
}
