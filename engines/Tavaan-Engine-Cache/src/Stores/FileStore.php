<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Stores;

use Tavaan\Engine\Cache\Contracts\StoreInterface;

/**
 * FileStore — درایور فایل با flock برای lock اتمیک.
 */
final class FileStore implements StoreInterface
{
    public function __construct(
        private readonly string $directory,
        private string $prefix = '',
    ) {
        if (!\is_dir($directory)) {
            \mkdir($directory, 0777, true);
        }
    }

    public function get(string $key): mixed
    {
        $path = $this->path($key);
        if (!\file_exists($path)) return null;
        $data = @\file_get_contents($path);
        if ($data === false) return null;
        $entry = \unserialize($data);
        if (!\is_array($entry) || !isset($entry['expires'], $entry['value'])) return null;
        if ($entry['expires'] !== 0 && $entry['expires'] <= \time()) {
            @\unlink($path);
            return null;
        }
        return $entry['value'];
    }

    public function many(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) $result[$key] = $this->get($key);
        return $result;
    }

    public function put(string $key, mixed $value, int $ttl): bool
    {
        $path = $this->path($key);
        $dir = \dirname($path);
        if (!\is_dir($dir)) \mkdir($dir, 0777, true);
        $expires = $ttl > 0 ? \time() + $ttl : 0;
        $data = \serialize(['value' => $value, 'expires' => $expires]);
        return (bool) @\file_put_contents($path, $data, \LOCK_EX);
    }

    public function putMany(array $values, int $ttl): bool
    {
        $ok = true;
        foreach ($values as $key => $value) {
            $ok = $this->put((string) $key, $value, $ttl) && $ok;
        }
        return $ok;
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
        return $this->put($key, $value, 0);
    }

    public function forget(string $key): bool
    {
        $path = $this->path($key);
        if (!\file_exists($path)) return true;
        return @\unlink($path);
    }

    public function flush(): bool
    {
        $files = \glob($this->directory . '/*') ?: [];
        foreach ($files as $f) {
            if (\is_file($f)) @\unlink($f);
        }
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

    private function path(string $key): string
    {
        $hash = \sha1($this->prefix . $key);
        return $this->directory . '/' . $hash . '.cache';
    }
}
