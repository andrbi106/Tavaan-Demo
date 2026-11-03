<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache;

use Tavaan\Engine\Cache\Contracts\RepositoryInterface;
use Tavaan\Engine\Cache\Contracts\StoreInterface;
use Tavaan\Engine\Cache\Stores\ArrayStore;
use Tavaan\Engine\Cache\Stores\FileStore;

/**
 * CacheRepository — API اصلی Cache.
 *
 * @see docs/03-engines/cache.md (DOC-060)
 */
final class CacheRepository implements RepositoryInterface
{
    private StoreInterface $store;
    private int $defaultTtl;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
        ?StoreInterface $store = null,
    ) {
        $this->defaultTtl = $config['default_ttl'] ?? 3600;
        $this->store = $store ?? $this->createDefaultStore();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->store->get($key);
        return $value === null ? $default : $value;
    }

    public function put(string $key, mixed $value, ?int $ttl = null): bool
    {
        return $this->store->put($key, $value, $ttl ?? $this->defaultTtl);
    }

    public function has(string $key): bool
    {
        return $this->store->has($key);
    }

    public function missing(string $key): bool
    {
        return !$this->has($key);
    }

    public function forget(string $key): bool
    {
        return $this->store->forget($key);
    }

    public function flush(): bool
    {
        return $this->store->flush();
    }

    public function forever(string $key, mixed $value): bool
    {
        return $this->store->forever($key, $value);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);
        if ($value !== null) return $value;
        $value = $callback();
        $this->put($key, $value, $ttl);
        return $value;
    }

    public function rememberForever(string $key, callable $callback): mixed
    {
        $value = $this->get($key);
        if ($value !== null) return $value;
        $value = $callback();
        $this->forever($key, $value);
        return $value;
    }

    public function add(string $key, mixed $value, ?int $ttl = null): bool
    {
        if ($this->has($key)) return false;
        return $this->put($key, $value, $ttl ?? $this->defaultTtl);
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->forget($key);
        return $value;
    }

    public function increment(string $key, int $value = 1): int
    {
        return $this->store->increment($key, $value);
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->store->decrement($key, $value);
    }

    public function many(array $keys): array
    {
        return $this->store->many($keys);
    }

    public function putMany(array $values, ?int $ttl = null): bool
    {
        return $this->store->putMany($values, $ttl ?? $this->defaultTtl);
    }

    public function store(?string $name = null): self
    {
        if ($name === null || $name === ($this->config['default'] ?? 'array')) {
            return $this;
        }
        return new self($this->config, $this->createStore($name));
    }

    public function getDefaultDriver(): string
    {
        return $this->config['default'] ?? 'array';
    }

    public function getStore(): StoreInterface
    {
        return $this->store;
    }

    private function createDefaultStore(): StoreInterface
    {
        $driver = $this->config['default'] ?? 'array';
        return $this->createStore($driver);
    }

    private function createStore(string $driver): StoreInterface
    {
        return match ($driver) {
            'array' => new ArrayStore($this->config['prefix'] ?? ''),
            'file' => new FileStore(
                directory: $this->config['file_dir'] ?? \sys_get_temp_dir() . '/tvn_cache',
                prefix: $this->config['prefix'] ?? '',
            ),
            default => new ArrayStore($this->config['prefix'] ?? ''),
        };
    }

    /**
     * lock
     */
    public function lock(?string $mode = null): self
    {
        return $this;
    }
}
