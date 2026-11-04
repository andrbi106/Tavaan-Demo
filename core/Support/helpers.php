<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Helper functions عمومی
 *
 * این فایل توسط autoload files در composer.json بارگذاری می‌شود.
 */

use Tavaan\Support\Arr;
use Tavaan\Support\Str;

if (!function_exists('array_get')) {
    function array_get(array $array, ?string $key, mixed $default = null): mixed
    {
        return Arr::get($array, $key, $default);
    }
}

if (!function_exists('array_set')) {
    function array_set(array &$array, string $key, mixed $value): void
    {
        Arr::set($array, $key, $value);
    }
}

if (!function_exists('str_random')) {
    function str_random(int $length = 16): string
    {
        return Str::random($length);
    }
}

if (!function_exists('str_slug')) {
    function str_slug(string $title, string $separator = '-'): string
    {
        return Str::slug($title, $separator);
    }
}

if (!function_exists('str_camel')) {
    function str_camel(string $value): string
    {
        return Str::camel($value);
    }
}

if (!function_exists('str_snake')) {
    function str_snake(string $value): string
    {
        return Str::snake($value);
    }
}

if (!function_exists('str_studly')) {
    function str_studly(string $value): string
    {
        return Str::studly($value);
    }
}
