<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat\Contracts;

/**
 * قرارداد کلاس Utf8 — کار با رشته‌های UTF-8.
 *
 * @see \Tavaan\Engine\Compat\Utf8
 * @api 1.0
 */
interface Utf8Interface
{
    public static function length(string $s): int;

    public static function substr(string $s, int $start, ?int $length = null): string;

    /**
     * @return list<string>
     */
    public static function split(string $s): array;

    public static function lower(string $s): string;

    public static function upper(string $s): string;

    public static function isValid(string $s): bool;

    public static function normalize(string $s, string $form = 'NFC'): string;

    public static function pad(string $s, int $length, string $pad = ' '): string;

    public static function ucfirst(string $s): string;
}
