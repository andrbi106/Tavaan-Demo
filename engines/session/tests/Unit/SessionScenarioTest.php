<?php

declare(strict_types=1);

namespace Tavaan\Engine\Session\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Session\Session;

/**
 * T-NN scenario tests for the session engine (real implementations).
 */
final class SessionScenarioTest extends TestCase
{
    public function test_t01_session_set_get(): void
    {
        // Scenario T-01
$s = new Session();
        $s->set("k", "v");
        $this->assertSame("v", $s->get("k"));
    }

    public function test_t02_session_has(): void
    {
        // Scenario T-02
$s = new Session();
        $s->set("x", 1);
        $this->assertTrue($s->has("x"));
        $this->assertFalse($s->has("y"));
    }
}
