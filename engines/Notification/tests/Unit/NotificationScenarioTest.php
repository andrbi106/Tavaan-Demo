<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notifications\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Notifications\SimpleNotifiable;

/**
 * T-NN scenario tests for the notification engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class NotificationScenarioTest extends TestCase
{
    public function test_t01_route_returns_channel_value(): void
    {
        // Scenario T-01
$n = new SimpleNotifiable(["mail" => "user@example.com", "sms" => "+989123456789"]);
        $this->assertSame("user@example.com", $n->routeNotificationFor("mail"));
        $this->assertSame("+989123456789", $n->routeNotificationFor("sms"));
    }
    public function test_t02_route_unknown_returns_null(): void
    {
        // Scenario T-02
$n = new SimpleNotifiable(["mail" => "user@example.com"]);
        $this->assertNull($n->routeNotificationFor("telegram"));
    }
    public function test_t03_toMail_returns_envelope_when_route_exists(): void
    {
        // Scenario T-03
$n = new SimpleNotifiable(["mail" => "u@e.com"]);
        $env = $n->toMail($n);
        $this->assertNotNull($env);
        $this->assertSame("mail", $env["channel"]);
        $this->assertSame("u@e.com", $env["to"]);
    }
}
