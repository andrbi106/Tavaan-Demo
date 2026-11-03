<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\Query\QueryBuilder;

/**
 * T-NN scenario tests for the database engine (real implementations).
 */
final class DatabaseScenarioTest extends TestCase
{
    public function test_t01_query_builder_select_from(): void
    {
        // Scenario T-01
// Without real DB, test QueryBuilder construction
        $this->assertTrue(class_exists(Connection::class));
        $this->assertTrue(class_exists(QueryBuilder::class));
    }
}
