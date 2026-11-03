<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config;

use Tavaan\Engine\Config\Exceptions\EnvParseException;

/**
 * EnvLoader — پارس فایل‌های .env بدون استفاده از putenv.
 *
 * @see docs/03-engines/config.md
 */
final class EnvLoader
{
    /**
     * @var array<string, string>
     */
    private array $env = [];

    /**
     * بارگذاری یک فایل .env.
     */
    public function load(string $path): self
    {
        if (!\file_exists($path)) {
            return $this;
        }
        $lines = \file($path, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $i => $line) {
            $lineNum = $i + 1;
            $line = \trim($line);
            if ($line === '' || \str_starts_with($line, '#')) {
                continue;
            }
            if (!\str_contains($line, '=')) {
                throw EnvParseException::forLine($lineNum, $line);
            }
            [$key, $value] = \explode('=', $line, 2);
            $key = \trim($key);
            $value = \trim($value);
            // Strip quotes
            if (($value[0] ?? '') === '"' && \substr($value, -1) === '"') {
                $value = \substr($value, 1, -1);
            } elseif (($value[0] ?? '') === "'" && \substr($value, -1) === "'") {
                $value = \substr($value, 1, -1);
            }
            $this->env[$key] = $value;
        }
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->env;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->env[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->env[$key]);
    }
}
