<?php
declare(strict_types=1);

namespace Tavaan\Support;

/**
 * محافظ مسیر — جلوگیری از path traversal.
 */
final class PathGuard
{
    /**
     * بررسی اینکه مسیر داخل basePath است (بدون path traversal).
     */
    public static function isSafe(string $path, string $basePath): bool
    {
        $realPath = realpath($path);
        if ($realPath === false) {
            return false;
        }
        $realBase = realpath($basePath);
        if ($realBase === false) {
            return false;
        }
        return str_starts_with($realPath, $realBase);
    }

    /**
     * نرمال‌سازی مسیر (حذف .. و .).
     */
    public static function normalize(string $path): string
    {
        $path = str_replace('\', '/', $path);
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                if (!empty($parts)) {
                    array_pop($parts);
                }
                continue;
            }
            $parts[] = $part;
        }
        $result = implode('/', $parts);
        if (str_starts_with($path, '/')) {
            $result = '/' . $result;
        }
        return $result;
    }

    /**
     * join مسیرها با /.
     */
    public static function join(string ...$parts): string
    {
        $result = [];
        foreach ($parts as $part) {
            $part = trim($part, '/');
            if ($part !== '') {
                $result[] = $part;
            }
        }
        return implode('/', $result);
    }
}
