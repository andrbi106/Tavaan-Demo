<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\I18n\Jalali\JalaliConverter;
use Tavaan\Engine\I18n\Jalali\JalaliDate;

/**
 * تست‌های Unit برای موتور Jalali.
 *
 * @see docs/03-engines/jalali.md (DOC-070)
 */
final class JalaliTest extends TestCase
{
    public function test_gregorian_to_jalali_known_date(): void
    {
        // 2026-01-01 → 1404/10/12
        [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali(2026, 1, 1);
        $this->assertSame(1404, $jy);
        $this->assertSame(10, $jm);
        $this->assertSame(11, $jd);
    }

    public function test_gregorian_to_jalali_first_day(): void
    {
        // 2024-03-20 → 1403/01/01 (نوروز)
        [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali(2024, 3, 20);
        $this->assertSame(1403, $jy);
        $this->assertSame(1, $jm);
        $this->assertSame(1, $jd);
    }

    public function test_gregorian_to_jalali_dec_31(): void
    {
        // 2024-12-31 → 1403/10/11
        [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali(2024, 12, 31);
        $this->assertSame(1403, $jy);
        $this->assertSame(10, $jm);
        $this->assertSame(11, $jd);
    }

    public function test_jalali_to_gregorian_roundtrip(): void
    {
        // Gregorian → Jalali → Gregorian should return the original
        for ($gy = 2000; $gy <= 2026; $gy += 4) {
            for ($gm = 1; $gm <= 12; $gm += 3) {
                [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali($gy, $gm, 15);
                [$rgy, $rgm, $rgd] = JalaliConverter::jalaliToGregorian($jy, $jm, $jd);
                $this->assertSame([$gy, $gm, 15], [$rgy, $rgm, $rgd],
                    "Failed for gregorian {$gy}-{$gm}-15 → jalali {$jy}/{$jm}/{$jd}");
            }
        }
    }

    public function test_is_leap_jalali(): void
    {
        // 1402 is not leap, 1403 is leap
        $this->assertFalse(JalaliConverter::isLeapJalali(1402));
        $this->assertTrue(JalaliConverter::isLeapJalali(1403));
    }

    public function test_jalali_month_length(): void
    {
        // First 6 months = 31 days
        for ($m = 1; $m <= 6; $m++) {
            $this->assertSame(31, JalaliConverter::jalaliMonthLength(1403, $m));
        }
        // Months 7-11 = 30 days
        for ($m = 7; $m <= 11; $m++) {
            $this->assertSame(30, JalaliConverter::jalaliMonthLength(1403, $m));
        }
        // Esfand in leap year = 30
        $this->assertSame(30, JalaliConverter::jalaliMonthLength(1403, 12));
        // Esfand in non-leap year = 29
        $this->assertSame(29, JalaliConverter::jalaliMonthLength(1402, 12));
    }

    public function test_day_of_year(): void
    {
        // 1403/01/01 = 1
        $this->assertSame(1, JalaliConverter::dayOfYear(1403, 1, 1));
        // 1403/01/31 = 31
        $this->assertSame(31, JalaliConverter::dayOfYear(1403, 1, 31));
        // 1403/02/01 = 32
        $this->assertSame(32, JalaliConverter::dayOfYear(1403, 2, 1));
        // 1403/12/30 (last day of leap year) = 366
        $this->assertSame(366, JalaliConverter::dayOfYear(1403, 12, 30));
    }

    public function test_jalali_date_format(): void
    {
        $str = JalaliDate::format(1403, 1, 15, 'Y/m/d');
        $this->assertSame('1403/01/15', $str);
    }

    public function test_jalali_date_format_with_month_name(): void
    {
        $str = JalaliDate::format(1403, 1, 15, 'Y/M/d');
        $this->assertSame('1403/فروردین/15', $str);
    }

    public function test_jalali_date_format_short_year(): void
    {
        $str = JalaliDate::format(1403, 1, 15, 'y/m/d');
        $this->assertSame('03/01/15', $str);
    }

    public function test_jalali_date_format_no_padded_day(): void
    {
        $str = JalaliDate::format(1403, 1, 5, 'Y/n/j');
        $this->assertSame('1403/1/5', $str);
    }

    public function test_month_name(): void
    {
        $this->assertSame('فروردین', JalaliDate::monthName(1));
        $this->assertSame('اسفند', JalaliDate::monthName(12));
        $this->assertSame('', JalaliDate::monthName(13));
    }

    public function test_season_name(): void
    {
        $this->assertSame('بهار', JalaliDate::seasonName(1));
        $this->assertSame('بهار', JalaliDate::seasonName(3));
        $this->assertSame('تابستان', JalaliDate::seasonName(4));
        $this->assertSame('تابستان', JalaliDate::seasonName(6));
        $this->assertSame('پاییز', JalaliDate::seasonName(7));
        $this->assertSame('زمستان', JalaliDate::seasonName(10));
        $this->assertSame('زمستان', JalaliDate::seasonName(12));
    }

    public function test_weekday(): void
    {
        // 1403/01/01 was a Wednesday (2024-03-20)
        $weekday = JalaliDate::weekday(1403, 1, 1);
        $this->assertSame('چهارشنبه', $weekday);
    }

    public function test_weekday_short(): void
    {
        $short = JalaliDate::weekdayShort(1403, 1, 1);
        $this->assertSame('چه', $short);
    }

    public function test_today_returns_array(): void
    {
        $today = JalaliDate::today();
        $this->assertCount(3, $today);
        // Today should be > 1400 (in 2026)
        $this->assertGreaterThan(1400, $today[0]);
    }

    public function test_from_timestamp(): void
    {
        // 2024-03-20 00:00:00 UTC = 1709164800
        // But local time may differ; use any known timestamp
        $ts = \mktime(0, 0, 0, 3, 20, 2024);
        [$jy, $jm, $jd] = JalaliDate::fromTimestamp($ts);
        $this->assertSame(1403, $jy);
        $this->assertSame(1, $jm);
        $this->assertSame(1, $jd);
    }

    public function test_month_names_count(): void
    {
        $this->assertCount(12, JalaliDate::MONTH_NAMES);
        $this->assertContains('فروردین', JalaliDate::MONTH_NAMES);
        $this->assertContains('اسفند', JalaliDate::MONTH_NAMES);
    }

    public function test_week_days_count(): void
    {
        $this->assertCount(7, JalaliDate::WEEK_DAYS);
    }

    public function test_season_names_count(): void
    {
        $this->assertCount(4, JalaliDate::SEASON_NAMES);
    }
}
