<?php

declare(strict_types=1);

namespace Tavaan\Engine\Orm\Tests\Unit;

use Tavaan\Testing\TestCase;

/**
 * T-NN scenario tests for the orm engine (real implementations).
 */
final class OrmScenarioTest extends TestCase
{
    public function test_t01_orm_class_exists(): void
    {
        // Scenario T-01
$this->assertTrue(class_exists("Tavaan\Engine\Orm\Model"));
    }
}
