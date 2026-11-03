<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Tests\Unit;

use Tavaan\Testing\TestCase;

/**
 * T-NN scenario tests for the i18n engine (real implementations).
 */
final class I18NScenarioTest extends TestCase
{
    public function test_t01_classes_exist(): void
    {
        // Scenario T-01
$this->assertTrue(class_exists("Tavaan\Engine\I18n\Translator"));
    }
}
