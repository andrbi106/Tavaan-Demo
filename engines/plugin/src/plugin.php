<?php
declare(strict_types=1);
namespace Tavaan\Engine\Plugin;

final class Plugin
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $version,
        public readonly string $path,
        public readonly bool $enabled = true,
        public readonly array $manifest = [],
    ) {}

    public function isActive(): bool { return $this->enabled && is_dir($this->path); }
    public function providers(): array { return $this->manifest['providers'] ?? []; }
    public function dependencies(): array { return $this->manifest['requires'] ?? []; }
}

final class PluginManager
{
    private array $plugins = [];
    private array $loaded = [];

    public function __construct(private readonly array $config = []) {}

    public function register(Plugin $plugin): self { $this->plugins[$plugin->id] = $plugin; return $this; }
    public function get(string $id): ?Plugin { return $this->plugins[$id] ?? null; }
    public function has(string $id): bool { return isset($this->plugins[$id]); }
    public function all(): array { return array_values($this->plugins); }
    public function enabled(): array { return array_values(array_filter($this->plugins, fn($p) => $p->enabled)); }

    public function load(string $id): void
    {
        $plugin = $this->get($id);
        if ($plugin === null || !$plugin->isActive()) return;
        foreach ($plugin->providers() as $provider) { if (class_exists($provider)) { new $provider(); } }
        $this->loaded[] = $id;
    }

    public function loadAll(): void { foreach ($this->plugins as $plugin) { $this->load($plugin->id); } }
    public function loadedPlugins(): array { return $this->loaded; }
    public function enable(string $id): void { if (isset($this->plugins[$id])) { $this->plugins[$id] = new Plugin($this->plugins[$id]->id, $this->plugins[$id]->name, $this->plugins[$id]->version, $this->plugins[$id]->path, true, $this->plugins[$id]->manifest); } }
    public function disable(string $id): void { if (isset($this->plugins[$id])) { $this->plugins[$id] = new Plugin($this->plugins[$id]->id, $this->plugins[$id]->name, $this->plugins[$id]->version, $this->plugins[$id]->path, false, $this->plugins[$id]->manifest); } }
}

final class PluginServiceProvider
{
    private ?PluginManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new PluginManager($this->config); }
    public function boot(): void { $this->manager?->loadAll(); }
    public function provides(): array { return [PluginManager::class, Plugin::class]; }
    public function getManager(): PluginManager { if ($this->manager === null) $this->register(); return $this->manager; }

    public function install(string $path): ?Plugin
    {
        if (!is_dir($path)) { return null; }
        $manifestFile = $path . '/plugin.json';
        if (!file_exists($manifestFile)) { return null; }
        $manifest = json_decode((string) @file_get_contents($manifestFile), true);
        if (!is_array($manifest)) { return null; }
        $plugin = new Plugin(
            $manifest['id'] ?? basename($path),
            $manifest['name'] ?? basename($path),
            $manifest['version'] ?? '0.0.1',
            $path,
            true,
            $manifest,
        );
        $this->getManager()->register($plugin);
        return $plugin;
    }
    public function list(): array
    {
        return $this->getManager()->all();
    }
    public function scan(string $dir): array
    {
        $found = [];
        if (!is_dir($dir)) { return $found; }
        foreach (new \DirectoryIterator($dir) as $entry) {
            if ($entry->isDot() || !$entry->isDir()) { continue; }
            $p = $this->install($entry->getPathname());
            if ($p !== null) { $found[] = $p; }
        }
        return $found;
    }
    public function uninstall(string $id): bool
    {
        $manager = $this->getManager();
        if (!$manager->has($id)) { return false; }
        $manager->disable($id);
        return true;
    }
    public function update(string $id, array $values): self
    {
        $manager = $this->getManager();
        if (!$manager->has($id)) { return $this; }
        $old = $manager->get($id);
        $manifest = array_merge($old->manifest, $values);
        $new = new Plugin($old->id, $old->name, $values['version'] ?? $old->version, $old->path, $old->enabled, $manifest);
        $manager->register($new);
        return $this;
    }
    public function verify(string $id): bool
    {
        $plugin = $this->getManager()->get($id);
        if ($plugin === null) { return false; }
        foreach ($plugin->dependencies() as $dep) {
            if (!$this->getManager()->has($dep)) { return false; }
        }
        return is_dir($plugin->path) && file_exists($plugin->path . '/plugin.json');
    }
}
