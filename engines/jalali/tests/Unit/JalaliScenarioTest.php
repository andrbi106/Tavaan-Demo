<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\I18n\Jalali\Jalali;
use Tavaan\Engine\I18n\Jalali\JalaliDate;

/**
 * T-NN scenario tests for the jalali engine (real implementations).
 */
final class JalaliScenarioTest extends TestCase
{
    public function test_t01_gregorian_to_jalali_conversion(): void
    {
        // Scenario T-01
$j = new Jalali();
        [$jy, $jm, $jd] = $j->toJalali(2026, 11, 4);
        $this->assertSame(1405, $jy);
    }

    public function test_t02_jalali_month_length(): void
    {
        // Scenario T-02
$j = new Jalali();
        $this->assertSame(31, $j->monthLength(1405, 1));
        $this->assertSame(30, $j->monthLength(1405, 7));
        $this->assertSame(29, $j->monthLength(1404, 12)); // non-leap
    }

    public function test_t03_jalali_format(): void
    {
        // Scenario T-03
$j = new Jalali();
        $formatted = $j->format(1405, 8, 13, "Y/m/d");
        $this->assertSame("1405/08/13", $formatted);
    }
}
