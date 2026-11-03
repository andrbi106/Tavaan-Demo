<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Routing\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Http\Routing\Router;

/**
 * T-NN scenario tests for the router engine (real implementations).
 */
final class RouterScenarioTest extends TestCase
{
    public function test_t01_static_match(): void
    {
        // Scenario T-01
$r = new Router();
        $r->get("/users", "UsersController@index");
        $d = $r->dispatch("GET", "/users");
        $this->assertNotNull($d);
    }

    public function test_t02_param_match(): void
    {
        // Scenario T-02
$r = new Router();
        $r->get("/users/{id}", "UsersController@show");
        $d = $r->dispatch("GET", "/users/42");
        $this->assertNotNull($d);
        $this->assertSame("42", $d["params"]["id"]);
    }

    public function test_t04_method_mismatch_returns_null(): void
    {
        // Scenario T-04
$r = new Router();
        $r->get("/users", "UsersController@index");
        $d = $r->dispatch("POST", "/users");
        $this->assertNull($d);
    }
}
