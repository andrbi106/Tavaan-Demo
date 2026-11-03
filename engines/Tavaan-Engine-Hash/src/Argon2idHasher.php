<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash;

use Tavaan\Engine\Hash\Contracts\HasherInterface;

/**
 * Argon2id Hasher با fallback به bcrypt.
 *
 * 🔒 threads=1 برای جلوگیری از side-channel attacks موازی.
 * 🔒 از password_hash/password_verify داخلی PHP استفاده می‌کند (constant-time).
 *
 * @see docs/03-engines/hash.md (DOC-055)
 */
final class Argon2idHasher implements HasherInterface
{
    public function __construct(
        private readonly int $memoryCost = 65536,
        private readonly int $timeCost = 4,
        private readonly int $threads = 1,
    ) {
    }

    public function make(string $value, array $options = []): string
    {
        $opts = \array_merge([
            'memory_cost' => $options['memory_cost'] ?? $this->memoryCost,
            'time_cost' => $options['time_cost'] ?? $this->timeCost,
            'threads' => $options['threads'] ?? $this->threads,
        ], $options);

        if (\defined('PASSWORD_ARGON2ID')) {
            $hash = \password_hash($value, \PASSWORD_ARGON2ID, $opts);
            if ($hash !== false && $hash !== null) {
                return $hash;
            }
        }
        // Fallback to bcrypt
        return $this->bcryptFallback($value, $opts['bcrypt_cost'] ?? 12);
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
        $opts = [
            'memory_cost' => $options['memory_cost'] ?? $this->memoryCost,
            'time_cost' => $options['time_cost'] ?? $this->timeCost,
            'threads' => $options['threads'] ?? $this->threads,
        ];
        if (\defined('PASSWORD_ARGON2ID')) {
            return \password_needs_rehash($hash, \PASSWORD_ARGON2ID, $opts);
        }
        return \password_needs_rehash($hash, \PASSWORD_BCRYPT, ['cost' => 12]);
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

    private function bcryptFallback(string $value, int $cost = 12): string
    {
        $hash = \password_hash($value, \PASSWORD_BCRYPT, ['cost' => $cost]);
        if ($hash === false) {
            throw new \RuntimeException('Failed to hash password.');
        }
        return $hash;
    }
}
