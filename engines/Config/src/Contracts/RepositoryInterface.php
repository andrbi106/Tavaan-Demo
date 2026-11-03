<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config\Contracts;

/**
 * قرارداد Repository پیکربندی.
 *
 * @see \Tavaan\Engine\Config\Repository
 * @api 1.0
 */
interface RepositoryInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string|array $key, mixed $value = null): void;

    public function has(string $key): bool;

    public function missing(string $key): bool;

    public function forget(string $key): void;

    public function pull(string $key, mixed $default = null): mixed;

    public function push(string $key, mixed $value): void;

    public function prepend(string $key, mixed $value): void;

    public function all(): array;

    /**
     * @param list<string> $keys
     */
    public function only(array $keys): array;

    /**
     * @param list<string> $keys
     */
    public function except(array $keys): array;

    public function namespace(string $ns): array;

    public function freeze(): void;

    public function isFrozen(): bool;

    public function toJson(bool $maskSecrets = true): string;

    /**
     * @param list<string> $keys
     */
    public function requiredKeys(array $keys): void;
}
