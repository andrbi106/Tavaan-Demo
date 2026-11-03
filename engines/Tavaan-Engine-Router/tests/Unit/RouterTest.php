<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Routing\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Routing\Router;
use Tavaan\Engine\Http\Routing\Route;

final class RouterTest extends TestCase
{
    public function test_basic(): void
    {
        $this->assertTrue(true);
    }

    public function test_router_get_dispatches(): void
    {
        $r = new Router();
        $r->get("/users", "UsersController@index");
        $d = $r->dispatch("GET", "/users");
        $this->assertNotNull($d);
    }

    public function test_router_post_dispatches(): void
    {
        $r = new Router();
        $r->post("/users", "UsersController@store");
        $d = $r->dispatch("POST", "/users");
        $this->assertNotNull($d);
    }

    public function test_router_dispatch_returns_null_for_unknown_route(): void
    {
        $r = new Router();
        $this->assertNull($r->dispatch("GET", "/nope"));
    }
}
