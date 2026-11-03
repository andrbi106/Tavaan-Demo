<?php

declare(strict_types=1);

namespace Tavaan\Engine\View;

/**
 * ViewFactory — ساخت و مدیریت ویوها.
 */
final class ViewFactory
{
    /**
     * @var array<string, string>
     */
    private array $sharedData = [];

    public function __construct(
        private readonly string $viewPath = 'views',
        private readonly ?string $compiledPath = null,
    ) {
    }

    public function make(string $template, array $data = []): View
    {
        $file = $this->resolveTemplate($template);
        $data = \array_merge($this->sharedData, $data);
        return new View($file, $data);
    }

    public function share(string $key, mixed $value): self
    {
        $this->sharedData[$key] = $value;
        return $this;
    }

    public function exists(string $template): bool
    {
        return \file_exists($this->resolveTemplate($template));
    }

    private function resolveTemplate(string $template): string
    {
        if (\is_file($template)) return $template;
        // Look in viewPath
        $file = \rtrim($this->viewPath, '/') . '/' . $template;
        if (\is_file($file)) return $file;
        $file .= '.tv.php';
        if (\is_file($file)) return $file;
        return $template;  // may be inline template
    }

    /**
     * addLocation
     */
    public function addLocation(string $location): self
    {
        $this->viewPath = $location;
        return $this;
    }
    /**
     * addNamespace
     */
    public function addNamespace(string $namespace, string $hint): self
    {
        $this->namespaces[$namespace] = $hint;
        return $this;
    }
    /**
     * component
     */
    public function component(string $name, string $class): self
    {
        $this->components[$name] = $class;
        return $this;
    }
    /**
     * composer
     */
    public function composer(string $view, callable $callback): self
    {
        $this->composers[$view] = $callback;
        return $this;
    }
    /**
     * directive
     */
    public function directive(string $name, callable $callback): self
    {
        $this->directives[$name] = $callback;
        return $this;
    }
    /**
     * file
     */
    public function file(string $key): string
    {
        return $this->viewPath . "/" . $key;
    }
    /**
     * first
     */
    public function first(array $views): ?string
    {
        foreach ($views as $view) {
            if ($this->exists($view)) return $view;
        }
        return null;
    }
    /**
     * renderEach
     */
    public function renderEach(string $view, array $data, string $iterator = "item"): string
    {
        $result = "";
        foreach ($data as $item) {
            $result .= $this->make($view, [$iterator => $item])->render();
        }
        return $result;
    }
}
