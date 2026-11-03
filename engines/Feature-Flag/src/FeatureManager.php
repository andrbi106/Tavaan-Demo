<?php

declare(strict_types=1);

namespace Tavaan\Engine\FeatureFlag;

/**
 * FeatureManager — runtime feature flags / experiments.
 */
final class FeatureManager
{
    /** @var array<string, array<string, mixed>> */
    private array $flags = [];
    /** @var array<string, callable> */
    private $resolvers = [];

    /** @param array<string, mixed> $config */
    public function __construct(private readonly array $config = [])
    {
        foreach ($config['flags'] ?? [] as $name => $flag) {
            $this->flags[$name] = array_merge(['enabled' => false, 'value' => null, 'percentage' => 100], $flag);
        }
    }

    /**
     * enabled — is the flag enabled (optionally for an actor).
     */
    public function enabled(string $name, mixed $actor = null): bool
    {
        $flag = $this->flags[$name] ?? null;
        if ($flag === null) { return false; }
        if (!($flag['enabled'] ?? false)) { return false; }
        $pct = $flag['percentage'] ?? 100;
        if ($pct < 100) {
            $bucket = $this->bucketFor($name, $actor);
            if ($bucket >= $pct) { return false; }
        }
        return true;
    }

    /**
     * for — check enabled for a specific actor (returns bool).
     */
    public function for(string $name, mixed $actor): bool
    {
        return $this->enabled($name, $actor);
    }

    /**
     * value — return the flag's typed value, or default if disabled.
     */
    public function value(string $name, mixed $default = null, mixed $actor = null): mixed
    {
        $flag = $this->flags[$name] ?? null;
        if ($flag === null) { return $default; }
        if (!$this->enabled($name, $actor)) { return $default; }
        return $flag['value'] ?? $default;
    }

    /**
     * define — register a flag at runtime.
     * @param array<string, mixed> $options
     */
    public function define(string $name, array $options = []): self
    {
        $this->flags[$name] = array_merge(['enabled' => false, 'value' => null, 'percentage' => 100], $options);
        return $this;
    }

    /**
     * kill — disable a flag immediately.
     */
    public function kill(string $name): self
    {
        if (isset($this->flags[$name])) { $this->flags[$name]['enabled'] = false; }
        return $this;
    }

    /**
     * revive — re-enable a killed flag.
     */
    public function revive(string $name): self
    {
        if (isset($this->flags[$name])) { $this->flags[$name]['enabled'] = true; }
        return $this;
    }

    /**
     * experiment — A/B variant selection based on actor bucket.
     * @param list<string> $variants
     */
    public function experiment(string $name, array $variants, mixed $actor = null): string
    {
        $bucket = $this->bucketFor($name, $actor);
        $index = $bucket % count($variants);
        return $variants[$index];
    }

    /**
     * percentage — return rollout percentage (0-100).
     */
    public function percentage(string $name): int
    {
        return (int) ($this->flags[$name]['percentage'] ?? 0);
    }

    private function bucketFor(string $name, mixed $actor): int
    {
        $key = $name . ':' . (string) $actor;
        $hash = crc32($key);
        return abs($hash) % 100;
    }

    /** @return array<string, array<string, mixed>> */
    public function all(): array { return $this->flags; }
}
