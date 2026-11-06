<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Cookie\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Cookie\Cookie;
use Tavaan\Engine\Http\Cookie\CookieJar;

final class CookieTest extends TestCase
{
    public function test_basic(): void
    {
        $this->assertTrue(true);
    }

    public function test_cookie_make_creates_instance(): void
    {
        $c = Cookie::make("name", "value", 10);
        $this->assertSame("name", $c->name);
        $this->assertSame("value", $c->value);
    }

    public function test_cookie_jar_set_get(): void
    {
        $jar = new CookieJar();
        $jar->set(Cookie::make("k", "v"));
        $this->assertTrue($jar->has("k"));
        $this->assertSame("v", $jar->get("k"));
    }

    public function test_cookie_jar_forget(): void
    {
        $jar = new CookieJar();
        $jar->set(Cookie::make("k", "v"));
        $jar->forget("k");
        $this->assertFalse($jar->has("k"));
    }
}
