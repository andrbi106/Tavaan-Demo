<?php
declare(strict_types=1);

namespace Tavaan\Engine\Auth\Guards;

/**
 * Token Guard — احراز هویت مبتنی بر API token.
 */
final class TokenGuard
{
    private mixed $user = null;

    public function __construct(
        public readonly string $name = 'api',
        private readonly array $config = [],
        private readonly mixed $hasher = null,
    ) {}

    public function attempt(array $credentials): bool
    {
        $token = $credentials['api_token'] ?? '';
        foreach ($this->config['users'] ?? [] as $user) {
            if (($user['api_token'] ?? '') === $token) {
                $this->user = $user;
                return true;
            }
        }
        return false;
    }

    public function user(): mixed { return $this->user; }
    public function id(): mixed { return $this->user['id'] ?? null; }
    public function check(): bool { return $this->user !== null; }
    public function guest(): bool { return !$this->check(); }
    public function login(mixed $user): void { $this->user = $user; }
    public function logout(): void { $this->user = null; }
}
