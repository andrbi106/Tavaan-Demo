<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config;

/**
 * ConfigLoader — بارگذاری فایل‌های پیکربندی PHP از دایرکتوری و merge.
 */
final class ConfigLoader
{
    /**
     * @param string $directory مسیر دایرکتوری config/
     */
    public function __construct(
        private readonly string $directory,
    ) {
    }

    /**
     * بارگذاری همهٔ فایل‌های *.php از دایرکتوری.
     *
     * @return array<string, mixed>
     */
    public function load(): array
    {
        if (!\is_dir($this->directory)) {
            return [];
        }
        $config = [];
        foreach (\glob("{$this->directory}/*.php") ?: [] as $file) {
            $name = \basename($file, '.php');
            $data = require $file;
            if (\is_array($data)) {
                $config[$name] = $data;
            }
        }
        return $config;
    }

    /**
     * بارگذاری یک فایل خاص.
     */
    public function loadFile(string $name): array
    {
        $file = "{$this->directory}/{$name}.php";
        if (!\file_exists($file)) {
            return [];
        }
        $data = require $file;
        return \is_array($data) ? $data : [];
    }

    /**
     * Merge چند منبع پیکربندی با اولویت (آخرین اولویت بالاتر).
     *
     * @param array<string, mixed> ...$sources
     * @return array<string, mixed>
     */
    public static function merge(array ...$sources): array
    {
        $result = [];
        foreach ($sources as $source) {
            $result = self::mergeRecursive($result, $source);
        }
        return $result;
    }

    /**
     * @param array<string, mixed> $a
     * @param array<string, mixed> $b
     * @return array<string, mixed>
     */
    private static function mergeRecursive(array $a, array $b): array
    {
        $result = $a;
        foreach ($b as $k => $v) {
            if (\is_array($v) && isset($result[$k]) && \is_array($result[$k])) {
                $result[$k] = self::mergeRecursive($result[$k], $v);
            } else {
                $result[$k] = $v;
            }
        }
        return $result;
    }
}
