<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash;

use Tavaan\Engine\Hash\Contracts\HasherInterface;

/**
 * HashManager — انتخاب Hasher بر اساس config.
 *
 * Facade: `Hash` → accessor `hash`.
 *
 * @see docs/03-engines/hash.md
 */
final class HashManager implements HasherInterface
{
    private HasherInterface $driver;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
        $this->driver = $this->createDriver();
    }

    public function make(string $value, array $options = []): string
    {
        return $this->driver->make($value, $options);
    }

    public function check(string $value, string $hash): bool
    {
        return $this->driver->check($value, $hash);
    }

    public function needsRehash(string $hash, array $options = []): bool
    {
        return $this->driver->needsRehash($hash, $options);
    }

    public function info(string $hash): array
    {
        return $this->driver->info($hash);
    }

    public function getDriver(): HasherInterface
    {
        return $this->driver;
    }

    private function createDriver(): HasherInterface
    {
        $driver = $this->config['driver'] ?? 'argon2id';
        return match ($driver) {
            'bcrypt' => new BcryptHasher(
                cost: $this->config['bcrypt']['cost'] ?? 12,
            ),
            default => new Argon2idHasher(
                memoryCost: $this->config['argon2id']['memory_cost'] ?? 65536,
                timeCost: $this->config['argon2id']['time_cost'] ?? 4,
                threads: $this->config['argon2id']['threads'] ?? 1,
            ),
        };
    }

    /**
     * driver — return current HasherInterface driver.
     */
    public function driver(): HasherInterface
    {
        return $this->driver;
    }
}
