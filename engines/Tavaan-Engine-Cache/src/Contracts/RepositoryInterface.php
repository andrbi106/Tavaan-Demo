<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Contracts;

use Tavaan\Engine\Cache\Contracts\StoreInterface;

/**
 * قرارداد Repository — API عمومی Cache.
 */
interface RepositoryInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value, ?int $ttl = null): bool;

    public function has(string $key): bool;

    public function missing(string $key): bool;

    public function forget(string $key): bool;

    public function flush(): bool;

    public function forever(string $key, mixed $value): bool;

    public function remember(string $key, int $ttl, callable $callback): mixed;

    public function rememberForever(string $key, callable $callback): mixed;

    public function add(string $key, mixed $value, ?int $ttl = null): bool;

    public function pull(string $key, mixed $default = null): mixed;

    public function increment(string $key, int $value = 1): int;

    public function decrement(string $key, int $value = 1): int;

    public function many(array $keys): array;

    public function putMany(array $values, ?int $ttl = null): bool;

    public function store(?string $name = null): self;

    public function getDefaultDriver(): string;
}
