<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali;

/**
 * Jalali — high-level facade over JalaliConverter.
 */
final class Jalali
{
    public function toJalali(int $gy, int $gm, int $gd): array
    {
        return JalaliConverter::gregorianToJalali($gy, $gm, $gd);
    }

    public function toGregorian(int $jy, int $jm, int $jd): array
    {
        return JalaliConverter::jalaliToGregorian($jy, $jm, $jd);
    }

    public function isLeap(int $jy): bool
    {
        // Jalali leap year: (year mod 33 ∈ {1, 5, 9, 13, 17, 22, 26, 30})
        $mod = $jy % 33;
        return in_array($mod, [1, 5, 9, 13, 17, 22, 26, 30], true);
    }

    public function monthLength(int $jy, int $jm): int
    {
        if ($jm <= 6) { return 31; }
        if ($jm <= 11) { return 30; }
        return $this->isLeap($jy) ? 30 : 29;
    }

    public function format(int $jy, int $jm, int $jd, string $format = 'Y/m/d'): string
    {
        return JalaliDate::format($jy, $jm, $jd, $format);
    }
}
