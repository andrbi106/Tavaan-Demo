<?php

declare(strict_types=1);

namespace Tavaan\Engine\View\Tests\Unit;

use Tavaan\Testing\TestCase;

/**
 * T-NN scenario tests for the view engine (real implementations).
 */
final class ViewScenarioTest extends TestCase
{
    public function test_t01_view_class_exists(): void
    {
        // Scenario T-01
$this->assertTrue(class_exists("Tavaan\Engine\View\View"));
    }
}
