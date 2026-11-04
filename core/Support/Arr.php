<?php
declare(strict_types=1);
namespace Tavaan\Support;

/** ابزارهای آرایه. */
final class Arr
{
    /** دسترسی به depth با dot notation: Arr::get($data, 'a.b.c', $default) */
    public static function get(array $array, string|null $key, mixed $default = null): mixed
    {
        if ($key === null || $key === '') {
            return $array;
        }
        $keys = explode('.', $key);
        $value = $array;
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        return $value;
    }

    public static function set(array &$array, string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $current = &$array;
        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $current[$k] = $value;
            } else {
                if (!isset($current[$k]) || !is_array($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }
        }
    }

    public static function has(array $array, string $key): bool
    {
        return self::get($array, $key, null) !== null;
    }

    /** @param list<string> $keys */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /** @param list<string> $keys */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item) && $depth > 0) {
                $result = array_merge($result, self::flatten($item, $depth - 1));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    public static function first(array $array, ?callable $callback = null): mixed
    {
        if ($callback === null) {
            return array_shift($array) ?? null;
        }
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }
}
