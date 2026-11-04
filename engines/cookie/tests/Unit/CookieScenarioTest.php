<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Cookie\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Cookie\Cookie;
use Tavaan\Engine\Http\Cookie\CookieJar;

/**
 * T-NN scenario tests for the cookie engine (real implementations).
 */
final class CookieScenarioTest extends TestCase
{
    public function test_t01_make_creates_cookie(): void
    {
        // Scenario T-01
$c = Cookie::make("name", "value", 10);
        $this->assertSame("name", $c->name);
        $this->assertSame("value", $c->value);
    }

    public function test_t04_cookie_defaults_httsonly(): void
    {
        // Scenario T-04
$c = new Cookie("k", "v");
        $this->assertTrue($c->httpOnly);
        $this->assertFalse($c->secure);
    }

    public function test_t05_forget_creates_expired_cookie(): void
    {
        // Scenario T-05
$jar = new CookieJar();
        $jar->set(Cookie::make("k", "v", 10));
        $jar->forget("k");
        // The forget queue should have an expired cookie
        $queued = $jar->getQueued();
        $this->assertCount(1, $queued);
    }
}
