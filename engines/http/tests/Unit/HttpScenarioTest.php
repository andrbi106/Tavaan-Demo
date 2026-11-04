<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Tests\Unit;

use Tavaan\Testing\TestCase;

/**
 * T-NN scenario tests for the http engine (real implementations).
 */
final class HttpScenarioTest extends TestCase
{
    public function test_t01_classes_exist(): void
    {
        // Scenario T-01
$this->assertTrue(class_exists("Tavaan\Engine\Http\Request"));
        $this->assertTrue(class_exists("Tavaan\Engine\Http\Response"));
    }
}
