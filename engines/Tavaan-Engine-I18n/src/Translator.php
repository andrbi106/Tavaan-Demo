<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n;

use Tavaan\Engine\I18n\Contracts\TranslatorInterface;

/**
 * Translator — موتور اصلی ترجمه.
 *
 * @see docs/03-engines/i18n.md (DOC-069)
 */
final class Translator implements TranslatorInterface
{
    private string $locale;

    /**
     * @var array<string, array<string, array<string, mixed>>> [namespace][locale][key] => value
     */
    private array $lines = [];

    /**
     * @var array<string, string> namespace => hint (file path prefix)
     */
    private array $namespaces = ['*' => ''];

    public function __construct(
        string $locale = 'fa',
        private readonly string $fallback = 'en',
        private readonly PluralRules $pluralizer = new PluralRules(),
    ) {
        $this->locale = $locale;
    }

    public function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale ??= $this->locale;
        $line = $this->lookup($key, $locale);
        if ($line === null) {
            // Try fallback
            if ($locale !== $this->fallback) {
                $line = $this->lookup($key, $this->fallback);
            }
        }
        if ($line === null) {
            return $key;
        }
        return $this->interpolate($line, $replace);
    }

    public function choice(string $key, int $number, array $replace = [], ?string $locale = null): string
    {
        $locale ??= $this->locale;
        $line = $this->get($key, $replace, $locale);
        $segments = \explode('|', $line);
        if (\count($segments) === 1) {
            return $this->interpolate($line, $replace + [':count' => (string) $number]);
        }
        $index = $this->pluralizer->indexFor($number, $locale);
        $chosen = $this->extractSegment($segments, $index, $number);
        return $this->interpolate($chosen, $replace + [':count' => (string) $number]);
    }

    public function has(string $key, ?string $locale = null): bool
    {
        $locale ??= $this->locale;
        return $this->lookup($key, $locale) !== null
            || $this->lookup($key, $this->fallback) !== null;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getFallback(): string
    {
        return $this->fallback;
    }

    public function addNamespace(string $namespace, string $hint): void
    {
        $this->namespaces[$namespace] = $hint;
    }

    public function addLines(array $lines, string $locale, string $namespace = '*'): void
    {
        foreach ($this->flatten($lines, '') as $k => $v) {
            $this->lines[$namespace][$locale][$k] = $v;
        }
    }

    /**
     * Load a translation file (returns array).
     *
     * @param string $file مسیر فایل (مثلاً lang/fa/validation.php)
     */
    public function loadFile(string $file, string $namespace = '*'): void
    {
        if (!\file_exists($file)) {
            return;
        }
        $data = require $file;
        if (\is_array($data)) {
            // Detect locale from path: lang/<locale>/...
            $parts = \explode('/', $file);
            $locale = $parts[\count($parts) - 2] ?? 'fa';
            $this->addLines($data, $locale, $namespace);
        }
    }

    /**
     * Load all files from a lang directory.
     */
    public function loadDirectory(string $dir): void
    {
        if (!\is_dir($dir)) {
            return;
        }
        foreach (\glob("{$dir}/*", \GLOB_ONLYDIR) ?: [] as $localeDir) {
            foreach (\glob("{$localeDir}/*.php") ?: [] as $file) {
                $this->loadFile($file);
            }
        }
    }

    private function lookup(string $key, string $locale): ?string
    {
        [$namespace, $cleanKey] = $this->parseKey($key);
        return $this->lines[$namespace][$locale][$cleanKey]
            ?? null;
    }

    /**
     * 'shop::product.title' → ['shop', 'product.title']
     *
     * @return array{0: string, 1: string}
     */
    private function parseKey(string $key): array
    {
        if (\str_contains($key, '::')) {
            [$ns, $k] = \explode('::', $key, 2);
            return [$ns, $k];
        }
        return ['*', $key];
    }

    private function interpolate(string $line, array $replace): string
    {
        foreach ($replace as $k => $v) {
            $line = \str_replace(':' . $k, (string) $v, $line);
        }
        return $line;
    }

    private function extractSegment(array $segments, int $index, int $number): string
    {
        // Look for explicit ranges like [2,10]:text
        foreach ($segments as $segment) {
            $segment = \trim($segment);
            if (\preg_match('/^\[(\d+),(\d+|\*)\]\s*(.+)$/s', $segment, $m)) {
                $min = (int) $m[1];
                $max = $m[2] === '*' ? \PHP_INT_MAX : (int) $m[2];
                if ($number >= $min && $number <= $max) {
                    return $m[3];
                }
            }
            if (\preg_match('/^\[(\d+)\]\s*(.+)$/s', $segment, $m)) {
                if ($number === (int) $m[1]) {
                    return $m[2];
                }
            }
        }
        // Fallback to index
        return \trim($segments[$index] ?? $segments[0]);
    }

    /**
     * Flatten nested array to dot-notation.
     *
     * @param array<string, mixed> $arr
     * @return array<string, string>
     */
    private function flatten(array $arr, string $prefix): array
    {
        $result = [];
        foreach ($arr as $k => $v) {
            $key = $prefix === '' ? (string) $k : "{$prefix}.{$k}";
            if (\is_array($v)) {
                $result = \array_merge($result, $this->flatten($v, $key));
            } else {
                $result[$key] = (string) $v;
            }
        }
        return $result;
    }

    /**
     * namespace
     */
    public function namespace(string $namespace): self
    {
        return $this;
    }
}
