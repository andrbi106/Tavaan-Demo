<?php
declare(strict_types=1);

namespace Tavaan\Engine\Auth\Guards;

use Tavaan\Engine\Auth\Contracts\Authenticatable;

/**
 * Session Guard — احراز هویت مبتنی بر session.
 *
 * این کلاس در فایل auth.php با namespace Tavaan\Engine\Auth تعریف شده بود.
 * اکنون با namespace صحیح Tavaan\Engine\Auth\Guards در فایل جداگانه قرار گرفت.
 */
final class SessionGuard
{
    private mixed $user = null;

    /** @var list<array<string, mixed>> */
    private array $users = [];

    public function __construct(
        public readonly string $name = 'web',
        private readonly array $config = [],
        private readonly mixed $hasher = null,
    ) {
        $this->users = $config['users'] ?? [];
    }

    public function attempt(array $credentials): bool
    {
        $email = $credentials['email'] ?? '';
        $password = $credentials['password'] ?? '';

        foreach ($this->users as $user) {
            if (($user['email'] ?? '') === $email) {
                if ($this->hasher !== null && method_exists($this->hasher, 'check')) {
                    if ($this->hasher->check($password, $user['password'] ?? '')) {
                        $this->login($user);
                        return true;
                    }
                } elseif (($user['password'] ?? '') === $password) {
                    $this->login($user);
                    return true;
                }
            }
        }
        return false;
    }

    public function login(mixed $user): void
    {
        $this->user = $user;
    }

    public function loginUsingId(mixed $id): bool
    {
        foreach ($this->users as $user) {
            if (($user['id'] ?? null) === $id) {
                $this->login($user);
                return true;
            }
        }
        return false;
    }

    public function logout(): void
    {
        $this->user = null;
    }

    public function user(): mixed
    {
        return $this->user;
    }

    public function id(): mixed
    {
        return $this->user['id'] ?? null;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function guest(): bool
    {
        return !$this->check();
    }
}
