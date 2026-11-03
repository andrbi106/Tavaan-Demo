<?php

declare(strict_types=1);

namespace Tavaan\Engine\Api\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Api\OpenApiGenerator;
use Tavaan\Engine\Api\JsonResource;

/**
 * T-NN scenario tests for the api engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class ApiScenarioTest extends TestCase
{
    public function test_t01_openapi_generate_includes_path(): void
    {
        // Scenario T-01
$gen = new OpenApiGenerator();
        $gen->generate("get", "/users", ["summary" => "List users"]);
        $json = $gen->toJson();
        $this->assertStringContainsString("/users", $json);
    }
    public function test_t02_openapi_to_yaml_multiline(): void
    {
        // Scenario T-02
$gen = new OpenApiGenerator();
        $gen->generate("post", "/orders");
        $yaml = $gen->toYaml();
        $this->assertStringContainsString("openapi:", $yaml);
        $this->assertStringContainsString("3.0.3", $yaml);
        $this->assertStringContainsString("/orders", $yaml);
    }
    public function test_t03_json_resource_make_single(): void
    {
        // Scenario T-03
$r = JsonResource::make(["id" => 1, "name" => "Ali"]);
        $arr = $r->toArray();
        $this->assertSame(1, $arr["id"]);
        $this->assertSame("Ali", $arr["name"]);
    }
    public function test_t04_json_resource_collection_count(): void
    {
        // Scenario T-04
$coll = JsonResource::collection([["id" => 1], ["id" => 2], ["id" => 3]]);
        $this->assertCount(3, $coll);
        $this->assertInstanceOf(JsonResource::class, $coll[0]);
    }
    public function test_t05_when_returns_value_or_default(): void
    {
        // Scenario T-05
$r = JsonResource::make(null);
        $this->assertSame("yes", $r->when(true, "yes", "no"));
        $this->assertSame("no", $r->when(false, "yes", "no"));
    }
}
