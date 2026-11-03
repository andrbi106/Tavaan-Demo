<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n;

/**
 * NumberFormatter — فرمت عدد و ارز بدون intl.
 */
final class NumberFormatter
{
    public function __construct(
        private readonly string $locale = 'fa',
    ) {
    }

    public function format(int|float $number, int $decimals = 0): string
    {
        $formatted = 
umber_format((float) $number, $decimals, '.', ',');
        return $this->locale === 'fa' ? $this->toPersianDigits($formatted) : $formatted;
    }

    public function currency(float $amount, string $currency = 'IRR'): string
    {
        $formatted = $this->format($amount, $amount % 1 === 0.0 ? 0 : 2);
        $symbol = match ($currency) {
            'IRR' => 'ریال',
            'USD' => '$',
            'EUR' => '€',
            default => $currency,
        };
        if ($currency === 'USD' || $currency === 'EUR') {
            return $symbol . $formatted;
        }
        return $formatted . ' ' . $symbol;
    }

    public function toPersianDigits(string $str): string
    {
        return \strtr($str, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹', ',' => '٬', '.' => '٫']);
    }

    public function fromPersianDigits(string $str): string
    {
        return \strtr($str, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٬' => ',', '٫' => '.']);
    }
}
