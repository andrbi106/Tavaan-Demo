<?php
declare(strict_types=1);
namespace Tavaan\Engine\Session;

final class Session
{
    private array $data = [];
    private string $id = '';
    private bool $started = false;
    private array $flash = [];

    public function __construct(private readonly array $config = []) {}

    public function start(): void { $this->started = true; $this->id = bin2hex(random_bytes(16)); if (session_status() === PHP_SESSION_NONE) session_start(); $this->data = &$_SESSION; }
    public function isStarted(): bool { return $this->started; }
    public function getId(): string { return $this->id; }
    public function get(string $key, mixed $default = null): mixed { return $this->data[$key] ?? $default; }
    public function set(string $key, mixed $value): void { $this->data[$key] = $value; }
    public function has(string $key): bool { return isset($this->data[$key]); }
    public function remove(string $key): void { unset($this->data[$key]); }
    public function all(): array { return $this->data; }
    public function flush(): void { $this->data = []; }
    public function regenerate(bool $destroy = false): void { if (session_status() === PHP_SESSION_ACTIVE) { session_regenerate_id($destroy); $this->id = session_id() ?: ''; } }
    public function flash(string $key, mixed $value): void { $this->data['__flash__'][$key] = $value; }
    public function getFlash(string $key, mixed $default = null): mixed { $v = $this->data['__flash__'][$key] ?? $default; return $v; }
    public function reflash(): void { if (isset($this->data['__flash__'])) { foreach ($this->data['__flash__'] as $k => $v) { $this->data['__flash__next'][$k] = $v; } } }
    public function keepFlash(array $keys): void { foreach ($keys as $k) { if (isset($this->data['__flash__'][$k])) { $this->data['__flash__next'][$k] = $this->data['__flash__'][$k]; } } }
    public function clearFlash(): void { if (isset($this->data['__flash__'])) { unset($this->data['__flash__']); } if (isset($this->data['__flash__next'])) { $this->data['__flash__'] = $this->data['__flash__next']; unset($this->data['__flash__next']); } }
    public function token(): string { if (!isset($this->data['_token'])) { $this->data['_token'] = bin2hex(random_bytes(16)); } return $this->data['_token']; }
    public function verifyToken(string $token): bool { return hash_equals($this->token(), $token); }
    public function save(): void { if (session_status() === PHP_SESSION_ACTIVE) { $this->clearFlash(); } }
}

final class SessionServiceProvider
{
    private ?Session $session = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->session = new Session($this->config); }
    public function boot(): void { $this->session?->start(); }
    public function provides(): array { return [Session::class]; }
    public function getSession(): Session { if ($this->session === null) $this->register(); return $this->session; }

    /**
     * exists
     */
    public function exists(): bool
    {
        return true;
    }
    /**
     * forget
     */
    public function forget(string $key): self
    {
        unset($this->data[$key]); return $this;
    }
    /**
     * id
     */
    public function id()
    {
        return $this->user["id"] ?? null;
    }
    /**
     * increment
     */
    public function increment(string $column, int $amount = 1): self
    {
        $this->setAttribute($column, ($this->getAttribute($column) ?? 0) + $amount); return $this;
    }
    /**
     * invalidate
     */
    public function invalidate(): void
    {
        $this->data = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
    /**
     * keep
     */
    public function keep(array $keys): void
    {
        foreach ($keys as $key) {
            if (isset($this->data["__flash__"][$key])) {
                $this->data["__flash__next"][$key] = $this->data["__flash__"][$key];
            }
        }
    }
    /**
     * migrate
     */
    public function migrate(bool $destroy = false): void
    {
        $this->regenerate($destroy);
    }
    /**
     * pull
     */
    public function pull(string $key, mixed $default = null)
    {
        $v = $this->get($key, $default); $this->forget($key); return $v;
    }
    /**
     * push
     */
    public function push(): self
    {
        return $this->save();
    }
    /**
     * put
     */
    public function put(string $key, mixed $value, ?int $ttl = null): self
    {
        $this->data[$key] = $value;
        if ($ttl !== null) {
            $this->data['__ttl__'][$key] = time() + $ttl;
        }
        return $this;
    }
}
