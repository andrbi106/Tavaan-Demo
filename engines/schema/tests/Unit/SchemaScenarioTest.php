<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema\Tests\Unit;

use Tavaan\Testing\TestCase;

/**
 * T-NN scenario tests for the schema engine (real implementations).
 */
final class SchemaScenarioTest extends TestCase
{
    public function test_t01_schema_class_exists(): void
    {
        // Scenario T-01
$this->assertTrue(class_exists("Tavaan\Engine\Database\Schema\SchemaBuilder"));
    }
}
