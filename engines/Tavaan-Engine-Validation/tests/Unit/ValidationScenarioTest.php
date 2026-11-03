<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Validation\Validator;

/**
 * T-NN scenario tests for the validation engine (real implementations).
 */
final class ValidationScenarioTest extends TestCase
{
    public function test_t01_validator_required_rule(): void
    {
        // Scenario T-01
$v = new Validator(["name" => ""], ["name" => "required"]);
        $this->assertFalse($v->passes());
    }

    public function test_t02_validator_required_pass(): void
    {
        // Scenario T-02
$v = new Validator(["name" => "Ali"], ["name" => "required"]);
        $this->assertTrue($v->passes());
    }
}
