<?php
declare(strict_types=1);
namespace Tavaan\Engine\Admin;

final class AdminManager
{
    private array $menuItems = [];
    private array $widgets = [];
    private array $settings = [];

    public function __construct(private readonly array $config = []) {}

    public function addMenuItem(string $section, string $title, string $url, ?string $icon = null, array $children = []): self
    {
        $this->menuItems[] = ['section' => $section, 'title' => $title, 'url' => $url, 'icon' => $icon, 'children' => $children];
        return $this;
    }

    public function addWidget(string $name, callable $callback, ?string $position = 'dashboard'): self
    {
        $this->widgets[$position][] = ['name' => $name, 'callback' => $callback];
        return $this;
    }

    public function setSetting(string $key, mixed $value): self { $this->settings[$key] = $value; return $this; }
    public function getSetting(string $key, mixed $default = null): mixed { return $this->settings[$key] ?? $default; }

    public function getMenu(): array { return $this->menuItems; }
    public function getWidgets(string $position = 'dashboard'): array { return $this->widgets[$position] ?? []; }
    public function getSettings(): array { return $this->settings; }

    public function renderMenu(): string
    {
        $html = '<ul class="admin-menu">';
        foreach ($this->menuItems as $item) {
            $html .= sprintf('<li class="menu-item"><a href="%s">%s</a></li>', $item['url'], $item['title']);
        }
        $html .= '</ul>';
        return $html;
    }
}

final class AdminServiceProvider
{
    private ?AdminManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new AdminManager($this->config); }
    public function boot(): void {}
    public function provides(): array { return [AdminManager::class]; }
    public function getManager(): AdminManager { if ($this->manager === null) $this->register(); return $this->manager; }

    public function dashboard(): array
    {
        $manager = $this->getManager();
        return [
            'widgets' => $manager->getWidgets('dashboard'),
            'settings' => $manager->getSettings(),
            'menu' => $manager->getMenu(),
        ];
    }
    public function menu(): array
    {
        return $this->getManager()->getMenu();
    }
    public function resource(string $name, string $controller): Resource
    {
        return new Resource($name, $controller);
    }
    public function widget(string $name, callable $callback, ?string $position = 'dashboard'): self
    {
        $this->getManager()->addWidget($name, $callback, $position);
        return $this;
    }
}
