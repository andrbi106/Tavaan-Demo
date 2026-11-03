<?php
declare(strict_types=1);
namespace Tavaan\Engine\Http\Cookie;

final class Cookie
{
    public function __construct(
        public readonly string $name,
        public readonly string $value = '',
        public readonly int $expires = 0,
        public readonly string $path = '/',
        public readonly string $domain = '',
        public readonly bool $secure = false,
        public readonly bool $httpOnly = true,
        public readonly ?string $sameSite = 'Lax',
    ) {}

    public static function make(string $name, string $value = '', int $minutes = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true): self
    {
        return new self($name, $value, $minutes > 0 ? time() + $minutes * 60 : 0);
    }

    public function isExpired(): bool
    {
        return $this->expires > 0 && $this->expires <= time();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name, 'value' => $this->value, 'expires' => $this->expires,
            'path' => $this->path, 'domain' => $this->domain, 'secure' => $this->secure,
            'httpOnly' => $this->httpOnly, 'sameSite' => $this->sameSite,
        ];
    }
}

final class CookieJar
{
    private array $cookies = [];
    private array $queued = [];

    public function __construct(private readonly array $config = []) {}

    public function set(Cookie $cookie): self
    {
        $this->queued[$cookie->name] = $cookie;
        return $this;
    }

    public function get(string $name, ?string $default = null): ?string
    {
        if (isset($this->cookies[$name])) return $this->cookies[$name];
        return $default;
    }

    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    public function forget(string $name): self
    {
        // Create a cookie that immediately expires (negative minutes = past timestamp)
        $this->set(Cookie::make($name, '', -1));
        unset($this->cookies[$name]);
        return $this;
    }

    public function fromArray(array $cookies): void
    {
        $this->cookies = array_map('strval', $cookies);
    }

    public function getQueued(): array
    {
        return array_values($this->queued);
    }

    public function flush(): void
    {
        $this->queued = [];
    }
}

final class CookieServiceProvider
{
    private ?CookieJar $jar = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->jar = new CookieJar($this->config); }
    public function boot(): void {}
    public function provides(): array { return [CookieJar::class, Cookie::class]; }
    public function getJar(): CookieJar { if ($this->jar === null) $this->register(); return $this->jar; }

    /**
     * forever
     */
    public function forever(string $key, mixed $value): self
    {
        $jar = $this->getJar();
        $jar->set(Cookie::make($key, (string) $value, 525600)); // 1 year in minutes
        return $this;
    }
    /**
     * hasQueued
     */
    public function hasQueued(string $key = null): bool
    {
        $queued = $this->getJar()->getQueued();
        if ($key === null) {
            return count($queued) > 0;
        }
        foreach ($queued as $cookie) {
            if ($cookie->name === $key) {
                return true;
            }
        }
        return false;
    }
    /**
     * queue
     */
    public function queue(string $name, string $value, int $minutes = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true): void
    {
        $this->getJar()->set(Cookie::make($name, $value, $minutes, $path, $domain, $secure, $httpOnly));
    }
    /**
     * queued
     */
    public function queued(string $key = null): array|\Tavaan\Engine\Http\Cookie\Cookie|null
    {
        $queued = $this->getJar()->getQueued();
        if ($key === null) {
            return $queued;
        }
        foreach ($queued as $cookie) {
            if ($cookie->name === $key) {
                return $cookie;
            }
        }
        return null;
    }
}
