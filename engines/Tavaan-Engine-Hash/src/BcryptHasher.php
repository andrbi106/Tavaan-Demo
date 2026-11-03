<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash;

use Tavaan\Engine\Hash\Contracts\HasherInterface;

/**
 * Bcrypt Hasher (fallback for systems without Argon2id).
 *
 * 🔒 cost=12 (توصیه‌شده برای 2024+)
 *
 * @see docs/03-engines/hash.md
 */
final class BcryptHasher implements HasherInterface
{
    public function __construct(
        private readonly int $cost = 12,
    ) {
    }

    public function make(string $value, array $options = []): string
    {
        $cost = $options['cost'] ?? $this->cost;
        $hash = \password_hash($value, \PASSWORD_BCRYPT, ['cost' => $cost]);
        if ($hash === false) {
            throw new \RuntimeException('Failed to hash password with bcrypt.');
        }
        return $hash;
    }

    public function check(string $value, string $hash): bool
    {
        if ($hash === '' || $value === '') {
            return false;
        }
        return \password_verify($value, $hash);
    }

    public function needsRehash(string $hash, array $options = []): bool
    {
        $cost = $options['cost'] ?? $this->cost;
        return \password_needs_rehash($hash, \PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    public function info(string $hash): array
    {
        $info = \password_get_info($hash);
        return [
            'algo' => $info['algoName'] ?? 'unknown',
            'algo_id' => $info['algo'] ?? 0,
            'options' => $info['options'] ?? [],
        ];
    }
}
