<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n;

/**
 * PluralRules — قوانین plural بدون intl.
 *
 * @see docs/03-engines/i18n.md §4.1
 */
final class PluralRules
{
    /**
     * تعداد حالت‌های plural برای هر زبان.
     *
     * @var array<string, int>
     */
    private const array RULES = [
        'fa' => 1,   // فارسی: one/other
        'en' => 2,   // one, other
        'ar' => 6,   // zero, one, two, few, many, other
        'ru' => 3,   // one, few, many
    ];

    public function indexFor(int $number, string $locale): int
    {
        $locale = \strtolower(\substr($locale, 0, 2));
        return match ($locale) {
            'fa' => $number === 1 ? 0 : 1,
            'en' => $number === 1 ? 0 : 1,
            'ar' => $this->arabicIndex($number),
            'ru' => $this->russianIndex($number),
            default => $number === 1 ? 0 : 1,
        };
    }

    private function arabicIndex(int $n): int
    {
        if ($n === 0) return 0;
        if ($n === 1) return 1;
        if ($n === 2) return 2;
        if ($n % 100 >= 3 && $n % 100 <= 10) return 3;
        if ($n % 100 >= 11) return 4;
        return 5;
    }

    private function russianIndex(int $n): int
    {
        $mod10 = $n % 10;
        $mod100 = $n % 100;
        if ($mod10 === 1 && $mod100 !== 11) return 0;
        if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) return 1;
        return 2;
    }
}
