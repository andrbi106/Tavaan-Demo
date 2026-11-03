<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Locks;

/**
 * CacheLock — lock اتمیک با owner و TTL.
 */
final class CacheLock
{
    private ?string $owner = null;

    public function __construct(
        private readonly string $name,
        private readonly int $ttl = 30,
        ?string $owner = null,
    ) {
        $this->owner = $owner ?? \bin2hex(\random_bytes(8));
    }

    public function acquire(): bool
    {
        // Simple in-memory; in real impl would use Redis SET NX or flock
        if (isset($GLOBALS['__tvn_locks'][$this->name])) return false;
        $GLOBALS['__tvn_locks'][$this->name] = $this->owner;
        return true;
    }

    public function release(): bool
    {
        if (($GLOBALS['__tvn_locks'][$this->name] ?? null) === $this->owner) {
            unset($GLOBALS['__tvn_locks'][$this->name]);
            return true;
        }
        return false;
    }

    public function block(int $seconds, callable $callback): mixed
    {
        $deadline = \time() + $seconds;
        while (!$this->acquire()) {
            if (\time() >= $deadline) {
                throw new \RuntimeException("Failed to acquire lock '{$this->name}' within {$seconds} seconds.");
            }
            \usleep(100_000);
        }
        try {
            return $callback();
        } finally {
            $this->release();
        }
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }
}
