<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Autoloader
 *
 * Autoloader صفر‌وابستگی با پشتیبانی از:
 *   - PSR-4 (prefix → path)
 *   - Wildcard (engines/*/src/)
 *   - Class map caching
 *   - Path traversal prevention (security)
 *   - Prefix locking (غیرقابل override)
 */

namespace Tavaan\Core\Autoloading;

final class Autoloader
{
    /** @var array<string, list<string>> prefix → paths */
    private array $prefixes = [];

    /** @var array<string, list<string>> wildcard prefixes (engines/*/src/) */
    private array $wildcards = [];

    /** @var array<string, string> class => file (cached) */
    private array $classMap = [];

    /** @var list<string> locked prefixes (cannot be overridden) */
    private array $locked = [];

    private bool $registered = false;

    public function __construct(
        private readonly string $basePath
    ) {}

    public static function create(string $basePath): self
    {
        return new self($basePath);
    }

    /**
     * افزودن نگاشت namespace → path(s).
     *
     * @param string       $prefix  namespace prefix (مثلاً "Tavaan\\Core\\")
     * @param string|list<string> $paths مسیرها
     */
    public function addNamespace(string $prefix, string|array $paths): self
    {
        $prefix = trim($prefix, '\\') . '\\';
        $paths = (array) $paths;

        foreach ($paths as $path) {
            $normalized = $this->normalizePath($path);

            // wildcard support: engines/*/src/
            if (str_contains($normalized, '*')) {
                $this->wildcards[$prefix][] = $normalized;
            } else {
                $this->prefixes[$prefix][] = $normalized;
            }
        }

        return $this;
    }

    /** افزودن class map. */
    public function addClassMap(array $map, bool $authoritative = false): self
    {
        foreach ($map as $class => $file) {
            $this->classMap[$class] = $file;
        }
        return $this;
    }

    /** قفل کردن یک prefix (غیرقابل override توسط موتورها). */
    public function lockPrefix(string $prefix): self
    {
        $this->locked[] = trim($prefix, '\\') . '\\';
        return $this;
    }

    /** ثبت autoloader در SPL. */
    public function register(): self
    {
        if ($this->registered) {
            return $this;
        }
        spl_autoload_register([$this, 'loadClass']);
        $this->registered = true;
        return $this;
    }

    /** حذف از SPL. */
    public function unregister(): self
    {
        if ($this->registered) {
            spl_autoload_unregister([$this, 'loadClass']);
            $this->registered = false;
        }
        return $this;
    }

    /**
     * بارگذاری یک کلاس.
     *
     * @return bool|null  true اگر بارگذاری شد، false اگر پیدا نشد، null اگر قبلاً بارگذاری شده
     */
    public function loadClass(string $class): ?bool
    {
        // امنیت: نام کلاس ناامن
        if (!preg_match('/^[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\\\\\x7f-\\xff]*$/', $class)) {
            throw AutoloadException::forUnsafeClassName($class);
        }

        // اگر قبلاً در class map
        if (isset($this->classMap[$class])) {
            $file = $this->classMap[$class];
            if ($this->isSafePath($file)) {
                require_once $file;
                return true;
            }
        }

        // جستجو در prefixes (longest prefix first)
        $file = $this->findFile($class);
        if ($file !== null) {
            require_once $file;
            $this->classMap[$class] = $file;
            return true;
        }

        return false;
    }

    /** یافتن مسیر فایل برای یک کلاس. */
    public function findFile(string $class): ?string
    {
        // class map
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }

        // longest-prefix-first search
        $sortedPrefixes = $this->prefixes;
        uksort($sortedPrefixes, fn($a, $b) => strlen($b) <=> strlen($a));

        foreach ($sortedPrefixes as $prefix => $paths) {
            if (!str_starts_with($class, $prefix)) {
                continue;
            }

            $relative = substr($class, strlen($prefix));
            $relativeFile = str_replace('\\', '/', $relative) . '.php';

            foreach ($paths as $path) {
                $file = $path . '/' . $relativeFile;
                if (is_file($file)) {
                    return $file;
                }
            }
        }

        // wildcard search
        foreach ($this->wildcards as $prefix => $patterns) {
            if (!str_starts_with($class, $prefix)) {
                continue;
            }
            $relative = substr($class, strlen($prefix));
            $relativeFile = str_replace('\\', '/', $relative) . '.php';

            foreach ($patterns as $pattern) {
                $wildcardPos = strpos($pattern, '*');
                $baseDir = substr($pattern, 0, $wildcardPos);
                $afterWildcard = substr($pattern, $wildcardPos + 1);

                $scanDir = $this->basePath . '/' . trim($baseDir, '/');
                if (!is_dir($scanDir)) {
                    continue;
                }

                foreach (new \DirectoryIterator($scanDir) as $entry) {
                    if ($entry->isDot() || !$entry->isDir()) {
                        continue;
                    }
                    $file = $entry->getPathname() . $afterWildcard . '/' . $relativeFile;
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }

        return null;
    }

    /** @return array<string, list<string>> */
    public function prefixes(): array { return $this->prefixes; }

    public function basePath(): string { return $this->basePath; }

    /**
     * بررسی امنیت مسیر (path traversal prevention).
     */
    private function isSafePath(string $file): bool
    {
        $realPath = realpath($file);
        if ($realPath === false) {
            return false;
        }
        // فایل باید داخل basePath باشد
        return str_starts_with($realPath, realpath($this->basePath) ?: $this->basePath);
    }

    private function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        if ($path === '') {
            return $this->basePath;
        }
        if ($path[0] !== '/' && !preg_match('#^[A-Za-z]:/#', $path)) {
            $path = $this->basePath . '/' . $path;
        }
        return rtrim($path, '/');
    }
}
