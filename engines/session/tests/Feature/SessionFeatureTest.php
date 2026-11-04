<?php

declare(strict_types=1);

namespace Tavaan\Engine\Session\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Session\Session;

/**
 * Feature tests for the session engine.
 */
final class SessionFeatureTest extends TestCase
{
    public function test_session_get_set_roundtrip(): void
    {
$s = new Session();
        $s->set("k1", "v1");
        $this->assertSame("v1", $s->get("k1"));
        $this->assertNull($s->get("missing"));
        $this->assertSame("def", $s->get("missing", "def"));
    }

    public function test_session_has(): void
    {
$s = new Session();
        $s->set("x", 1);
        $this->assertTrue($s->has("x"));
        $this->assertFalse($s->has("y"));
    }

    public function test_session_remove(): void
    {
$s = new Session();
        $s->set("x", 1);
        $s->remove("x");
        $this->assertFalse($s->has("x"));
    }

    public function test_session_flush_clears_all(): void
    {
$s = new Session();
        $s->set("a", 1);
        $s->set("b", 2);
        $s->flush();
        $this->assertSame([], $s->all());
    }

    public function test_session_put_returns_self_for_chaining(): void
    {
$s = new Session();
        $s->set("k", "v");
        $this->assertSame("v", $s->get("k"));
    }

    public function test_session_all_returns_data(): void
    {
$s = new Session();
        $s->set("x", 1);
        $s->set("y", 2);
        $all = $s->all();
        $this->assertSame(1, $all["x"]);
        $this->assertSame(2, $all["y"]);
    }
}
