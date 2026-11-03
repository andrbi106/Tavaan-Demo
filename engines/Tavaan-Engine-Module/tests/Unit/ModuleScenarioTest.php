<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Module\ModuleManager;

/**
 * T-NN scenario tests for the module engine (real implementations).
 */
final class ModuleScenarioTest extends TestCase
{
    public function test_t01_module_manager_class_exists(): void
    {
        // Scenario T-01
$this->assertTrue(class_exists(ModuleManager::class));
    }
}
