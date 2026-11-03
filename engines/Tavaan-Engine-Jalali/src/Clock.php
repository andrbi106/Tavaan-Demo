<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali;

/**
 * Clock — provides the current time in UTC and Jalali formats.
 */
final class Clock
{
    public function utcNow(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    public function jalaliNow(string $format = 'Y/m/d H:i:s'): string
    {
        $now = $this->now();
        $gy = (int) $now->format('Y');
        $gm = (int) $now->format('m');
        $gd = (int) $now->format('d');
        [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali($gy, $gm, $gd);
        $datePart = JalaliDate::format($jy, $jm, $jd, $format);
        // Replace time fields if format contains H/i/s
        $timePart = $now->format('H:i:s');
        if (str_contains($format, 'H')) {
            $datePart .= ' ' . $timePart;
        }
        return $datePart;
    }

    public function timestamp(): int
    {
        return time();
    }
}
