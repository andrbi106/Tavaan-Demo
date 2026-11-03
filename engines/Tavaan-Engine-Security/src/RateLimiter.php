<?php

declare(strict_types=1);

namespace Tavaan\Engine\Security;

final class RateLimiter
{
    private array $hits = [];
    private int $maxAttempts;
    private int $decaySeconds;

    public function __construct(int $maxAttempts = 60, int $decaySeconds = 60)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    public function attempt(string $key, callable $callback): mixed
    {
        if ($this->tooManyAttempts($key)) {
            return false;
        }
        $this->hit($key);
        return $callback();
    }

    public function tooManyAttempts(string $key): bool
    {
        $this->cleanup($key);
        return ($this->hits[$key] ?? 0) >= $this->maxAttempts;
    }

    public function hit(string $key, int $decaySeconds = 0): int
    {
        $this->cleanup($key);
        $decay = $decaySeconds > 0 ? $decaySeconds : $this->decaySeconds;
        $this->hits[$key] = ($this->hits[$key] ?? 0) + 1;
        $this->expiry[$key] = time() + $decay;
        return $this->hits[$key];
    }

    public function remaining(string $key): int
    {
        $this->cleanup($key);
        return max(0, $this->maxAttempts - ($this->hits[$key] ?? 0));
    }

    public function clear(string $key): self
    {
        unset($this->hits[$key], $this->expiry[$key]);
        return $this;
    }

    public function availableIn(string $key): int
    {
        if (!$this->tooManyAttempts($key)) return 0;
        return ($this->expiry[$key] ?? 0) - time();
    }

    private array $expiry = [];

    private function cleanup(string $key): void
    {
        if (isset($this->expiry[$key]) && $this->expiry[$key] <= time()) {
            unset($this->hits[$key], $this->expiry[$key]);
        }
    }
}
