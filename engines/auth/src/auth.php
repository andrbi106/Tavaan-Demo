<?php
declare(strict_types=1);
namespace Tavaan\Engine\Auth;

use Tavaan\Engine\Auth\Guards\SessionGuard;
use Tavaan\Engine\Auth\Guards\TokenGuard;

final class AuthManager
{
    private mixed $user = null;
    private array $guards = [];

    public function __construct(private readonly array $config = [], private $hasher = null, private $session = null)
    {
        $this->guards['web'] = new \Tavaan\Engine\Auth\Guards\SessionGuard('web', $config, $hasher);
        $this->guards['api'] = new \Tavaan\Engine\Auth\Guards\TokenGuard('api', $config, $hasher);
    }

    public function guard(?string $name = null): \Tavaan\Engine\Auth\Guards\SessionGuard|\Tavaan\Engine\Auth\Guards\TokenGuard
    {
        $name ??= $this->config['default'] ?? 'web';
        return $this->guards[$name] ?? $this->guards['web'];
    }

    public function user(): mixed { return $this->guard()->user(); }
    public function id(): mixed { return $this->guard()->id(); }
    public function check(): bool { return $this->guard()->check(); }
    public function guest(): bool { return !$this->check(); }
    public function attempt(array $credentials): bool { return $this->guard()->attempt($credentials); }
    public function login(mixed $user): void { $this->guard()->login($user); }
    public function logout(): void { $this->guard()->logout(); }
    public function loginUsingId(mixed $id): bool { return $this->guard()->loginUsingId($id); }
}