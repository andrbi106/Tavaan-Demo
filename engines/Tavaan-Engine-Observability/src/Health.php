<?php

declare(strict_types=1);

namespace Tavaan\Engine\Observability;

/**
 * Health — liveness and readiness checks.
 */
final class Health
{
    /** @var array<string, callable> */
    private array $checks = [];
    private array $statuses = [];

    public function add(string $key, mixed $value, ?int $ttl = null): self
    {
        if (is_callable($value)) {
            $this->checks[$key] = $value;
        } else {
            $this->statuses[$key] = ['value' => $value, 'ttl' => $ttl, 'ts' => time()];
        }
        return $this;
    }

    public function check(): bool
    {
        foreach ($this->checks as $name => $cb) {
            try {
                $ok = (bool) $cb();
                $this->statuses[$name] = ['healthy' => $ok, 'ts' => time()];
                if (!$ok) { return false; }
            } catch (\Throwable $e) {
                $this->statuses[$name] = ['healthy' => false, 'error' => $e->getMessage(), 'ts' => time()];
                return false;
            }
        }
        return true;
    }

    /**
     * live — liveness probe: is the process itself up?
     */
    public function live(): bool
    {
        // Process is alive if this method can be called.
        return true;
    }

    /**
     * ready — readiness probe: are all dependencies ok?
     * @return array<string, mixed>
     */
    public function ready(): array
    {
        $result = ['healthy' => true, 'checks' => []];
        foreach ($this->checks as $name => $cb) {
            try {
                $ok = (bool) $cb();
                $result['checks'][$name] = ['healthy' => $ok];
                if (!$ok) { $result['healthy'] = false; }
            } catch (\Throwable $e) {
                $result['checks'][$name] = ['healthy' => false, 'error' => $e->getMessage()];
                $result['healthy'] = false;
            }
        }
        return $result;
    }

    /** @return array<string, mixed> */
    public function statuses(): array
    {
        return $this->statuses;
    }
}
