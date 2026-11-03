<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config;

use Tavaan\Engine\Config\Contracts\RepositoryInterface;
use Tavaan\Engine\Config\Exceptions\ConfigException;
use Tavaan\Engine\Config\Exceptions\ConfigFrozenException;

/**
 * Repository پیکربندی با dot-notation access.
 *
 * @see docs/03-engines/config.md (DOC-032)
 */
final class Repository implements RepositoryInterface
{
    /** @var array<string, mixed> */
    private array $items;

    private bool $frozen = false;

    /**
     * @var array<string, mixed> LRU cache برای dot-path lookups
     */
    private array $cache = [];

    /**
     * @param array<string, mixed> $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (\array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }
        if (\array_key_exists($key, $this->items)) {
            return $this->cache[$key] = $this->items[$key];
        }
        $segments = \explode('.', $key);
        $value = $this->items;
        foreach ($segments as $segment) {
            if (!\is_array($value) || !\array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        // Cache only simple types (avoid caching arrays to prevent mutation issues)
        if (!\is_array($value)) {
            $this->cache[$key] = $value;
        }
        return $value;
    }

    public function set(string|array $key, mixed $value = null): void
    {
        if ($this->frozen) {
            throw new ConfigFrozenException();
        }
        $this->cache = [];  // invalidate cache
        if (\is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
            return;
        }
        $segments = \explode('.', $key);
        $current = &$this->items;
        foreach ($segments as $i => $segment) {
            if (!\is_array($current)) {
                $current = [];
            }
            if (!\array_key_exists($segment, $current)) {
                $current[$segment] = [];
            }
            if ($i === \count($segments) - 1) {
                $current[$segment] = $value;
                return;
            }
            $current = &$current[$segment];
        }
    }

    public function has(string $key): bool
    {
        $result = $this->get($key, $this);
        return $result !== $this;
    }

    public function missing(string $key): bool
    {
        return !$this->has($key);
    }

    public function forget(string $key): void
    {
        if ($this->frozen) {
            throw new ConfigFrozenException();
        }
        $this->cache = [];
        $segments = \explode('.', $key);
        $current = &$this->items;
        foreach ($segments as $i => $segment) {
            if (!\is_array($current) || !\array_key_exists($segment, $current)) {
                return;
            }
            if ($i === \count($segments) - 1) {
                unset($current[$segment]);
                return;
            }
            $current = &$current[$segment];
        }
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }

    public function push(string $key, mixed $value): void
    {
        if ($this->frozen) {
            throw new ConfigFrozenException();
        }
        $arr = $this->get($key, []);
        if (!\is_array($arr)) {
            throw new ConfigException("Key '{$key}' is not an array, cannot push.");
        }
        $arr[] = $value;
        $this->set($key, $arr);
    }

    public function prepend(string $key, mixed $value): void
    {
        if ($this->frozen) {
            throw new ConfigFrozenException();
        }
        $arr = $this->get($key, []);
        if (!\is_array($arr)) {
            throw new ConfigException("Key '{$key}' is not an array, cannot prepend.");
        }
        \array_unshift($arr, $value);
        $this->set($key, $arr);
    }

    public function all(): array
    {
        return $this->items;
    }

    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $this->setArrayPath($result, $key, $this->get($key));
            }
        }
        return $result;
    }

    public function except(array $keys): array
    {
        $result = $this->items;
        foreach ($keys as $key) {
            $this->unsetArrayPath($result, $key);
        }
        return $result;
    }

    public function namespace(string $ns): array
    {
        $result = $this->get($ns);
        return \is_array($result) ? $result : [];
    }

    public function freeze(): void
    {
        $this->frozen = true;
    }

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    public function toJson(bool $maskSecrets = true): string
    {
        $items = $this->items;
        if ($maskSecrets) {
            $items = $this->maskSecrets($items);
        }
        return (string) \json_encode($items, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
    }

    public function requiredKeys(array $keys): void
    {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                throw new ConfigException("Required config key '{$key}' is missing.");
            }
        }
    }

    /**
     * Mask secret keys (those containing 'password', 'secret', 'token', 'key', 'api').
     *
     * @param array<string, mixed> $items
     */
    private function maskSecrets(array $items, string $prefix = ''): array
    {
        $result = [];
        foreach ($items as $k => $v) {
            $path = $prefix === '' ? (string) $k : $prefix . '.' . $k;
            if (\is_array($v)) {
                $result[$k] = $this->maskSecrets($v, $path);
                continue;
            }
            if (\preg_match('/(password|secret|token|key|api|passwd|pwd|passphrase)/i', $path)) {
                $result[$k] = '***masked***';
                continue;
            }
            $result[$k] = $v;
        }
        return $result;
    }

    /**
     * @param array<string, mixed> $arr
     */
    private function setArrayPath(array &$arr, string $key, mixed $value): void
    {
        $segments = \explode('.', $key);
        $current = &$arr;
        foreach ($segments as $i => $segment) {
            if ($i === \count($segments) - 1) {
                $current[$segment] = $value;
                return;
            }
            if (!isset($current[$segment]) || !\is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }
    }

    /**
     * @param array<string, mixed> $arr
     */
    private function unsetArrayPath(array &$arr, string $key): void
    {
        $segments = \explode('.', $key);
        $current = &$arr;
        foreach ($segments as $i => $segment) {
            if (!\is_array($current) || !\array_key_exists($segment, $current)) {
                return;
            }
            if ($i === \count($segments) - 1) {
                unset($current[$segment]);
                return;
            }
            $current = &$current[$segment];
        }
    }

    /**
     * masked
     */
    public function masked(): array
    {
        return $this->maskSecrets($this->all());
    }
}
