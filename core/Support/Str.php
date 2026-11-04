<?php
declare(strict_types=1);
namespace Tavaan\Support;

/** ابزارهای رشته. */
final class Str
{
    /** تولید رشتهٔ تصادفی. */
    public static function random(int $length = 16): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($chars, (int) ceil($length / strlen($chars)))), 0, $length);
    }

    /** slug: تبدیل به URL-safe. */
    public static function slug(string $title, string $separator = '-'): string
    {
        $title = preg_replace('/[^\p{L}\p{N}\s-]/u', '', mb_strtolower($title)) ?? '';
        $title = preg_replace('/[\s-]+/', $separator, trim($title)) ?? '';
        return trim($title, $separator);
    }

    /** camelCase از snake_case. */
    public static function camel(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $value))));
    }

    /** snake_case از camelCase. */
    public static function snake(string $value): string
    {
        return strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value) ?? $value);
    }

    /** studly_case: PascalCase */
    public static function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $value)));
    }

    /** شروع با رشته? */
    public static function startsWith(string $haystack, string $needle): bool
    {
        return str_starts_with($haystack, $needle);
    }

    /** پایان با رشته? */
    public static function endsWith(string $haystack, string $needle): bool
    {
        return str_ends_with($haystack, $needle);
    }

    /** truncation با طول مشخص. */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }
        return mb_substr($value, 0, $limit) . $end;
    }
}
