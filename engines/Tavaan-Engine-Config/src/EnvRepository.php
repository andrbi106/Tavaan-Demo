<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config;

/**
 * EnvRepository — دسترسی نوع‌دار به متغیرهای محیطی.
 *
 * @see docs/03-engines/config.md §4.3
 */
final class EnvRepository
{
    public function __construct(
        private readonly EnvLoader $loader = new EnvLoader(),
    ) {
    }

    public function get(string $key, ?string $default = null): ?string
    {
        // First check .env loaded values
        if ($this->loader->has($key)) {
            return $this->loader->get($key);
        }
        // Then check $_ENV, $_SERVER, getenv()
        if (\array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        if (\array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }
        $val = \getenv($key);
        return $val === false ? $default : $val;
    }

    public function string(string $key, string $default = ''): string
    {
        $val = $this->get($key);
        return $val === null ? $default : $val;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $val = $this->get($key);
        if ($val === null) {
            return $default;
        }
        $lower = \strtolower($val);
        return \in_array($lower, ['true', '1', 'yes', 'on'], true);
    }

    public function int(string $key, int $default = 0): int
    {
        $val = $this->get($key);
        if ($val === null || !\is_numeric($val)) {
            return $default;
        }
        return (int) $val;
    }

    public function float(string $key, float $default = 0.0): float
    {
        $val = $this->get($key);
        if ($val === null || !\is_numeric($val)) {
            return $default;
        }
        return (float) $val;
    }

    /**
     * @return list<string>
     */
    public function list(string $key, string $separator = ','): array
    {
        $val = $this->get($key);
        if ($val === null) {
            return [];
        }
        $parts = \explode($separator, $val);
        return \array_map(\trim(...), \array_filter($parts, fn($s) => \trim($s) !== ''));
    }

    public function json(string $key): ?array
    {
        $val = $this->get($key);
        if ($val === null) {
            return null;
        }
        $decoded = \json_decode($val, true);
        if (!\is_array($decoded)) {
            return null;
        }
        return $decoded;
    }

    /**
     * @param list<string> $keys
     * @throws \RuntimeException اگر یکی از کلیدها موجود نباشد
     */
    public function required(array $keys): void
    {
        foreach ($keys as $key) {
            if ($this->get($key) === null) {
                throw new \RuntimeException("Required env var '{$key}' is missing.");
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function all(bool $masked = false): array
    {
        $all = [];
        foreach ($this->loader->all() as $k => $v) {
            $all[$k] = $v;
        }
        foreach ($_ENV as $k => $v) {
            $all[$k] = $v;
        }
        if ($masked) {
            foreach ($all as $k => $v) {
                if (\preg_match('/(password|secret|token|key|api|passwd|pwd|passphrase)/i', $k)) {
                    $all[$k] = '***masked***';
                }
            }
        }
        return $all;
    }

    /**
     * has
     */
    public function has(string $key): bool
    {
        return false;
    }
}
