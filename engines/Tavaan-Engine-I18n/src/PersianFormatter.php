<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n;

/**
 * PersianFormatter — پیاده‌سازی مطابق مستندات.
 *
 * @see docs/03-engines/ (YAML machine-readable block)
 */
final class PersianFormatter
{
    /**
     * digits
     */
    public function digits(string $text): string
    {
        return strtr($text, ["0"=>"۰","1"=>"۱","2"=>"۲","3"=>"۳","4"=>"۴","5"=>"۵","6"=>"۶","7"=>"۷","8"=>"۸","9"=>"۹"]);
    }
    /**
     * number
     */
    public function number(int|float $n, int $decimals = 0): string
    {
        return $this->digits(number_format((float)$n, $decimals, ".", ","));
    }
    /**
     * currency
     */
    public function currency(float $amount, string $currency = "ریال"): string
    {
        return $this->number($amount) . " " . $currency;
    }
    /**
     * date
     */
    public function date(string $format, ?int $timestamp = null): string
    {
        $ts = $timestamp ?? time();
        $gy = (int)date("Y", $ts);
        $gm = (int)date("n", $ts);
        $gd = (int)date("j", $ts);
        [$jy, $jm, $jd] = [0,0,0];
        $jy = $gy - 621;
        $result = str_replace(["Y","m","d"], [sprintf("%04d",$jy), sprintf("%02d",$gm), sprintf("%02d",$gd)], $format);
        return $this->digits($result);
    }
    /**
     * toLatinDigits
     */
    public function toLatinDigits(string $text): string
    {
        return strtr($text, ["۰"=>"0","۱"=>"1","۲"=>"2","۳"=>"3","۴"=>"4","۵"=>"5","۶"=>"6","۷"=>"7","۸"=>"8","۹"=>"9"]);
    }
}
