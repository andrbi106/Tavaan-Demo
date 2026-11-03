<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali;

/**
 * JalaliConverter — تبدیل دوطرفه جلالی ↔ میلادی با الگوریتم دقیق.
 *
 * @see docs/03-engines/jalali.md (DOC-070)
 */
final class JalaliConverter
{
    /**
     * تبدیل میلادی به جلالی.
     *
     * @return array{0: int, 1: int, 2: int} [year, month, day]
     */
    public static function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $gdm = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + \intdiv($gy2 + 3, 4) - \intdiv($gy2 + 99, 100)
              + \intdiv($gy2 + 399, 400) + $gd + $gdm[$gm - 1];

        $jy = -1595 + (33 * \intdiv($days, 12053));
        $days %= 12053;
        $jy += 4 * \intdiv($days, 1461);
        $days %= 1461;

        if ($days > 365) {
            $jy += \intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jm = 1 + \intdiv($days, 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + \intdiv($days - 186, 30);
            $jd = 1 + (($days - 186) % 30);
        }
        return [$jy, $jm, $jd];
    }

    /**
     * تبدیل جلالی به میلادی.
     *
     * @return array{0: int, 1: int, 2: int} [year, month, day]
     */
    public static function jalaliToGregorian(int $jy, int $jm, int $jd): array
    {
        $jy += 1595;
        $days = -355668 + (365 * $jy) + (\intdiv($jy, 33) * 8) + \intdiv(($jy % 33) + 3, 4)
              + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);

        $gy = 400 * \intdiv($days, 146097);
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * \intdiv(--$days, 36524);
            $days %= 36524;
            if ($days >= 365) {
                $days++;
            }
        }
        $gy += 4 * \intdiv($days, 1461);
        $days %= 1461;
        if ($days > 365) {
            $gy += \intdiv($days - 1, 365);
            $days = ($days - 1) % 365;
        }

        $gd = $days + 1;
        $salA = [0, 31, ((($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28),
            31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $gm = 0;
        for ($i = 1; $i <= 12 && $gd > $salA[$i]; $i++) {
            $gd -= $salA[$i];
            $gm = $i;
        }
        return [$gy, $gm + 1, $gd];
    }

    /**
     * بررسی سال کبیسه جلالی.
     */
    public static function isLeapJalali(int $jy): bool
    {
        $mod = (($jy - ($jy > 0 ? 474 : 473)) % 2820) + 474 + 38;
        return ((($mod * 682) % 2816) < 682);
    }

    /**
     * طول ماه جلالی.
     */
    public static function jalaliMonthLength(int $jy, int $jm): int
    {
        if ($jm <= 6) return 31;
        if ($jm <= 11) return 30;
        return self::isLeapJalali($jy) ? 30 : 29;  // اسفند
    }

    /**
     * روزهای سپری‌شده از سال جلالی.
     */
    public static function dayOfYear(int $jy, int $jm, int $jd): int
    {
        $days = 0;
        for ($m = 1; $m < $jm; $m++) {
            $days += self::jalaliMonthLength($jy, $m);
        }
        return $days + $jd;
    }
}
