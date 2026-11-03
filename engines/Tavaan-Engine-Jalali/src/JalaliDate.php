<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali;

/**
 * JalaliDate — کار با تاریخ جلالی (فرمت، نام روز/ماه).
 */
final class JalaliDate
{
    private int $jy;
    private int $jm;
    private int $jd;

    public function __construct(int $jy = 0, int $jm = 1, int $jd = 1)
    {
        $this->jy = $jy;
        $this->jm = $jm;
        $this->jd = $jd;
    }

    /** @var list<string> */
    public const array MONTH_NAMES = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
    ];

    /** @var list<string> */
    public const array WEEK_DAYS = [
        'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه'
    ];

    /** @var list<string> */
    public const array SEASON_NAMES = ['بهار', 'تابستان', 'پاییز', 'زمستان'];

    /**
     * فرمت تاریخ جلالی با pattern.
     *
     * Y: سال ۴ رقمی، y: سال ۲ رقمی
     * m: ماه ۲ رقمی، n: ماه بدون صفر، M: نام ماه
     * d: روز ۲ رقمی، j: روز بدون صفر
     *
     * @return string
     */
    public static function format(int $jy, int $jm, int $jd, string $format = 'Y/m/d'): string
    {
        $replacements = [
            'Y' => \sprintf('%04d', $jy),
            'y' => \sprintf('%02d', $jy % 100),
            'm' => \sprintf('%02d', $jm),
            'n' => (string) $jm,
            'M' => self::monthName($jm),
            'd' => \sprintf('%02d', $jd),
            'j' => (string) $jd,
            'D' => self::weekdayShort($jy, $jm, $jd),
            'l' => self::weekday($jy, $jm, $jd),
            'F' => self::seasonName($jm),
        ];
        return \strtr($format, $replacements);
    }

    public static function monthName(int $jm): string
    {
        return self::MONTH_NAMES[$jm - 1] ?? '';
    }

    public static function seasonName(int $jm): string
    {
        $season = (int) \ceil($jm / 3);  // 1-4
        return self::SEASON_NAMES[$season - 1] ?? '';
    }

    /**
     * نام روز هفته (جلالی) — محاسبه از میلادی.
     */
    public static function weekday(int $jy, int $jm, int $jd): string
    {
        [$gy, $gm, $gd] = JalaliConverter::jalaliToGregorian($jy, $jm, $jd);
        $ts = \mktime(12, 0, 0, $gm, $gd, $gy);
        $dow = (int) \date('w', $ts);  // 0 (Sunday) - 6 (Saturday)
        return self::WEEK_DAYS[$dow];
    }

    public static function weekdayShort(int $jy, int $jm, int $jd): string
    {
        $full = self::weekday($jy, $jm, $jd);
        return \mb_substr($full, 0, 2, 'UTF-8');
    }

    /**
     * تاریخ امروز جلالی.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    public static function today(): array
    {
        $ts = \time();
        $gy = (int) \date('Y', $ts);
        $gm = (int) \date('m', $ts);
        $gd = (int) \date('d', $ts);
        return JalaliConverter::gregorianToJalali($gy, $gm, $gd);
    }

    /**
     * تبدیل timestamp به جلالی.
     *
     * @return array{0: int, 1: int, 2: int}
     */
    public static function fromTimestamp(int $timestamp): array
    {
        $gy = (int) \date('Y', $timestamp);
        $gm = (int) \date('m', $timestamp);
        $gd = (int) \date('d', $timestamp);
        return JalaliConverter::gregorianToJalali($gy, $gm, $gd);
    }

    /**
     * addDays — return a new instance shifted by $days days.
     */
    public function addDays(int $days): self
    {
        [$gy, $gm, $gd] = JalaliConverter::jalaliToGregorian($this->jy, $this->jm, $this->jd);
        $ts = mktime(12, 0, 0, $gm, $gd + $days, $gy);
        $newGy = (int) date('Y', $ts);
        $newGm = (int) date('m', $ts);
        $newGd = (int) date('d', $ts);
        [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali($newGy, $newGm, $newGd);
        return new self($jy, $jm, $jd);
    }
    /**
     * dayName — weekday name in Persian.
     */
    public function dayName(): string
    {
        return self::weekday($this->jy, $this->jm, $this->jd);
    }
    /**
     * diffInDays — return number of days between this and another JalaliDate.
     * @param JalaliDate|\DateTimeInterface $other
     */
    public function diffInDays(mixed $other): int
    {
        $ts1 = $this->toTimestamp();
        if ($other instanceof \DateTimeInterface) {
            $ts2 = $other->getTimestamp();
        } elseif ($other instanceof self) {
            $ts2 = $other->toTimestamp();
        } else {
            $ts2 = (int) $other;
        }
        return (int) round(abs($ts2 - $ts1) / 86400);
    }

    public function toTimestamp(): int
    {
        [$gy, $gm, $gd] = JalaliConverter::jalaliToGregorian($this->jy, $this->jm, $this->jd);
        return mktime(12, 0, 0, $gm, $gd, $gy);
    }
    /**
     * endOfMonth — return last day of the current Jalali month.
     */
    public function endOfMonth(): self
    {
        $len = (new Jalali())->monthLength($this->jy, $this->jm);
        return new self($this->jy, $this->jm, $len);
    }
    /**
     * fromGregorian — set this instance from a Gregorian date.
     */
    public function fromGregorian(int $gy, int $gm, int $gd): self
    {
        [$jy, $jm, $jd] = JalaliConverter::gregorianToJalali($gy, $gm, $gd);
        $this->jy = $jy;
        $this->jm = $jm;
        $this->jd = $jd;
        return $this;
    }
    /**
     * isLeap — is the current year a Jalali leap year?
     */
    public function isLeap(): bool
    {
        return (new Jalali())->isLeap($this->jy);
    }
    /**
     * now — return the current Jalali date formatted.
     */
    public function now(string $format = 'Y/m/d'): string
    {
        [$jy, $jm, $jd] = self::today();
        $this->jy = $jy;
        $this->jm = $jm;
        $this->jd = $jd;
        return self::format($jy, $jm, $jd, $format);
    }
    /**
     * parse — parse "YYYY/MM/DD" (Jalali) and return a formatted string.
     */
    public function parse(string $date): string
    {
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            $this->jy = (int) $parts[0];
            $this->jm = (int) $parts[1];
            $this->jd = (int) $parts[2];
        }
        return self::format($this->jy, $this->jm, $this->jd, 'Y/m/d');
    }
    /**
     * startOfMonth — return day 1 of the current Jalali month.
     */
    public function startOfMonth(): self
    {
        return new self($this->jy, $this->jm, 1);
    }
    /**
     * subMonths — subtract months from the current date.
     */
    public function subMonths(int $months): self
    {
        $totalMonths = $this->jy * 12 + ($this->jm - 1) - $months;
        $newJy = intdiv($totalMonths, 12);
        $newJm = $totalMonths % 12 + 1;
        $day = min($this->jd, (new Jalali())->monthLength($newJy, $newJm));
        return new self($newJy, $newJm, $day);
    }
    /**
     * toGregorian — return array [gy, gm, gd] from this Jalali date.
     * @return array{0: int, 1: int, 2: int}
     */
    public function toGregorian(): array
    {
        return JalaliConverter::jalaliToGregorian($this->jy, $this->jm, $this->jd);
    }
}
