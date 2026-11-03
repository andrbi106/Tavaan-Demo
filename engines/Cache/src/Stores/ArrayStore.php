<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Stores;

use Tavaan\Engine\Cache\Contracts\StoreInterface;

/**
 * ArrayStore — درایور درون‌حافظه‌ای (سریع‌ترین، فقط برای یک request).
 *
 * @see docs/03-engines/cache.md
 */
final class ArrayStore implements StoreInterface
{
    /** @var array<string, array{value: mixed, expires: int|null}> */
    private array $data = [];

    private string $prefix = '';

    public function __construct(string $prefix = '') {
        $this->prefix = $prefix;
    }

    public function get(string $key): mixed
    {
        $k = $this->prefix . $key;
        if (!isset($this->data[$k])) return null;
        $entry = $this->data[$k];
        if ($entry['expires'] !== null && $entry['expires'] <= \time()) {
            unset($this->data[$k]);
            return null;
        }
        return $entry['value'];
    }

    public function many(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }

    public function put(string $key, mixed $value, int $ttl): bool
    {
        $k = $this->prefix . $key;
        $expires = $ttl > 0 ? \time() + $ttl : null;
        $this->data[$k] = ['value' => $value, 'expires' => $expires];
        return true;
    }

    public function putMany(array $values, int $ttl): bool
    {
        foreach ($values as $key => $value) {
            $this->put((string) $key, $value, $ttl);
        }
        return true;
    }

    public function increment(string $key, int $value = 1): int
    {
        $current = (int) $this->get($key);
        $new = $current + $value;
        $this->put($key, $new, 3600);
        return $new;
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->increment($key, -$value);
    }

    public function forever(string $key, mixed $value): bool
    {
        $k = $this->prefix . $key;
        $this->data[$k] = ['value' => $value, 'expires' => null];
        return true;
    }

    public function forget(string $key): bool
    {
        $k = $this->prefix . $key;
        unset($this->data[$k]);
        return true;
    }

    public function flush(): bool
    {
        $this->data = [];
        return true;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
