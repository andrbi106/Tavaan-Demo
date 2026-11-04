<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Contracts;

/**
 * قرارداد Store — هر درایور cache این را پیاده‌سازی می‌کند.
 */
interface StoreInterface
{
    public function get(string $key): mixed;

    public function many(array $keys): array;

    public function put(string $key, mixed $value, int $ttl): bool;

    public function putMany(array $values, int $ttl): bool;

    public function increment(string $key, int $value = 1): int;

    public function decrement(string $key, int $value = 1): int;

    public function forever(string $key, mixed $value): bool;

    public function forget(string $key): bool;

    public function flush(): bool;

    public function has(string $key): bool;

    public function getPrefix(): string;
}
