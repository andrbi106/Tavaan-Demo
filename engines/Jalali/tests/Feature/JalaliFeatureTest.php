<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\I18n\Jalali\JalaliConverter;
use Tavaan\Engine\I18n\Jalali\JalaliDate;
use Tavaan\Engine\I18n\Jalali\JalaliServiceProvider;

/**
 * تست‌های Feature برای موتور Jalali.
 */
final class JalaliFeatureTest extends TestCase
{
    public function test_service_provider_register_no_op(): void
    {
        $provider = new JalaliServiceProvider();
        $provider->register();
        $this->assertTrue(true);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new JalaliServiceProvider();
        $this->assertContains(JalaliConverter::class, $provider->provides());
        $this->assertContains(JalaliDate::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/jalali.php';
        $this->assertArrayHasKey('default', $config);
        $this->assertArrayHasKey('first_day_of_week', $config);
        $this->assertArrayHasKey('show_weekdays', $config);
        $this->assertArrayHasKey('persian_digits', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/jalali.php';
        $this->assertArrayHasKey('month_farvardin', $messages);
        $this->assertArrayHasKey('weekday_saturday', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/jalali.php';
        $this->assertArrayHasKey('month_farvardin', $messages);
        $this->assertSame('Farvardin', $messages['month_farvardin']);
    }

    public function test_end_to_end_date_conversion(): void
    {
        // Gregorian 2024-03-20 → Jalali 1403/01/01 → Gregorian 2024-03-20
        [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali(2024, 3, 20);
        $this->assertSame(1403, $jy);
        $this->assertSame(1, $jm);
        $this->assertSame(1, $jd);
        [$rgy, $rgm, $rgd] = JalaliConverter::jalaliToGregorian($jy, $jm, $jd);
        $this->assertSame([2024, 3, 20], [$rgy, $rgm, $rgd]);

        // Format
        $formatted = JalaliDate::format($jy, $jm, $jd, 'Y M d');
        $this->assertSame('1403 فروردین 1', $formatted);
    }
}
