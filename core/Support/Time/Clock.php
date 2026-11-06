<?php
declare(strict_types=1);

namespace Tavaan\Support\Time;

/**
 * ساعت مونوtonic برای اندازه‌گیری زمان دقیق.
 */
final class Clock
{
    /**
     * زمان فعلی به‌صورت microtime (seconds with microseconds).
     */
    public static function now(): float
    {
        return microtime(true);
    }

    /**
     * زمان فعلی به‌صورت nanoseconds.
     */
    public static function nanos(): int
    {
        return hrtime(true);
    }

    /**
     * قالب‌بندی زمان با فرمت مشخص.
     */
    public static function format(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }

    /**
     * زمان Unix (seconds).
     */
    public static function unix(): int
    {
        return time();
    }
}
