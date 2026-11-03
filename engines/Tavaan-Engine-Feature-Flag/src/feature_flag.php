<?php
declare(strict_types=1);
namespace Tavaan\Engine\FeatureFlag;

final class FeatureFlagManager
{
    private array $flags = [];

    public function __construct(private readonly array $config = [])
    {
        foreach ($config['flags'] ?? [] as $name => $value) { $this->flags[$name] = $value; }
    }

    public function set(string $name, bool $enabled): void { $this->flags[$name] = $enabled; }
    public function get(string $name, bool $default = false): bool { return $this->flags[$name] ?? $default; }
    public function isOn(string $name): bool { return $this->get($name, false) === true; }
    public function isOff(string $name): bool { return !$this->isOn($name); }
    public function has(string $name): bool { return isset($this->flags[$name]); }
    public function all(): array { return $this->flags; }
    public function enable(string $name): void { $this->flags[$name] = true; }
    public function disable(string $name): void { $this->flags[$name] = false; }
    public function remove(string $name): void { unset($this->flags[$name]); }
    public function count(): int { return count($this->flags); }
    public function enabledFlags(): array { return array_keys(array_filter($this->flags, fn($v) => $v === true)); }
    public function disabledFlags(): array { return array_keys(array_filter($this->flags, fn($v) => $v !== true)); }
}

final class FeatureFlagServiceProvider
{
    private ?FeatureFlagManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new FeatureFlagManager($this->config); }
    public function boot(): void {}
    public function provides(): array { return [FeatureFlagManager::class]; }
    public function getManager(): FeatureFlagManager { if ($this->manager === null) $this->register(); return $this->manager; }
}
