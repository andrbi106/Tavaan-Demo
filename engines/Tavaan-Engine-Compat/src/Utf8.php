<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat;

use Tavaan\Engine\Compat\Contracts\Utf8Interface;

/**
 * کار با رشته‌های UTF-8 بدون وابستگی به mbstring.
 *
 * این کلاس توابع `mb_*` را با fallback خالص PHP ارائه می‌دهد.
 * موتورهای دیگر فقط باید از این کلاس استفاده کنند (نه `mb_*` مستقیم).
 *
 * @see docs/03-engines/compat.md §4.1
 */
final class Utf8 implements Utf8Interface
{
    /**
     * جدول نگاشت لاتین بزرگ به کوچک.
     * برای حروف خارج از لاتین (فارسی، عربی، یونانی، سیریلیک) باید از intl یا mbstring استفاده شود.
     */
    private const array UPPER_MAP = [
        'A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd', 'E' => 'e',
        'F' => 'f', 'G' => 'g', 'H' => 'h', 'I' => 'i', 'J' => 'j',
        'K' => 'k', 'L' => 'l', 'M' => 'm', 'N' => 'n', 'O' => 'o',
        'P' => 'p', 'Q' => 'q', 'R' => 'r', 'S' => 's', 'T' => 't',
        'U' => 'u', 'V' => 'v', 'W' => 'w', 'X' => 'x', 'Y' => 'y',
        'Z' => 'z',
        // Turkish/Azeri
        'I' => 'ı',
    ];

    private const array LOWER_MAP = [
        'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E',
        'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J',
        'k' => 'K', 'l' => 'L', 'm' => 'M', 'n' => 'N', 'o' => 'O',
        'p' => 'P', 'q' => 'Q', 'r' => 'R', 's' => 'S', 't' => 'T',
        'u' => 'U', 'v' => 'V', 'w' => 'W', 'x' => 'X', 'y' => 'Y',
        'z' => 'Z',
        'ı' => 'I',
    ];

    public static function length(string $s): int
    {
        if (\function_exists('mb_strlen')) {
            return \mb_strlen($s, 'UTF-8');
        }
        return \preg_match_all('/./us', $s) ?: 0;
    }

    public static function substr(string $s, int $start, ?int $length = null): string
    {
        if (\function_exists('mb_substr')) {
            return \mb_substr($s, $start, $length, 'UTF-8');
        }
        // Negative start
        if ($start < 0) {
            $len = self::length($s);
            $start = \max(0, $len + $start);
        }
        if ($length === null) {
            $pattern = '/^.{' . $start . '}(.*)$/us';
        } else {
            $length = $length < 0 ? self::length($s) + $length - $start : $length;
            $pattern = '/^.{' . $start . '}(.{0,' . $length . '})/us';
        }
        return \preg_match($pattern, $s, $m) === 1 ? $m[1] : '';
    }

    /**
     * @return list<string>
     */
    public static function split(string $s): array
    {
        if (\function_exists('mb_str_split')) {
            return \mb_str_split($s, 1, 'UTF-8') ?: [];
        }
        \preg_match_all('/./us', $s, $m);
        return $m[0];
    }

    public static function lower(string $s): string
    {
        if (\function_exists('mb_strtolower')) {
            return \mb_strtolower($s, 'UTF-8');
        }
        return \strtr($s, self::UPPER_MAP);
    }

    public static function upper(string $s): string
    {
        if (\function_exists('mb_strtoupper')) {
            return \mb_strtoupper($s, 'UTF-8');
        }
        return \strtr($s, self::LOWER_MAP);
    }

    public static function isValid(string $s): bool
    {
        if (\function_exists('mb_check_encoding')) {
            return \mb_check_encoding($s, 'UTF-8');
        }
        return \preg_match('//u', $s) === 1;
    }

    public static function normalize(string $s, string $form = 'NFC'): string
    {
        if (\class_exists(\Normalizer::class)) {
            $constant = 'Normalizer::FORM_' . $form;
            if (\defined($constant)) {
                $normalized = \Normalizer::normalize($s, \constant($constant));
                if ($normalized !== false) {
                    return $normalized;
                }
            }
        }
        // Without intl, full normalization is not possible
        return $s;
    }

    /**
     * Pad a multibyte string to a certain length with another string.
     */
    public static function pad(string $s, int $length, string $pad = ' '): string
    {
        $len = self::length($s);
        if ($len >= $length) {
            return $s;
        }
        $padLen = self::length($pad);
        if ($padLen === 0) {
            return $s;
        }
        $target = $length - $len;
        $repeats = (int) \ceil($target / $padLen);
        $fullPad = \str_repeat($pad, $repeats);
        return $s . self::substr($fullPad, 0, $target);
    }

    /**
     * Convert first character of string to uppercase.
     */
    public static function ucfirst(string $s): string
    {
        if ($s === '') {
            return '';
        }
        $first = self::substr($s, 0, 1);
        $rest = self::substr($s, 1);
        return self::upper($first) . $rest;
    }
}
