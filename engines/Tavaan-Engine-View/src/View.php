<?php

declare(strict_types=1);

namespace Tavaan\Engine\View;

/**
 * View — یک نمونه ویو قابل render.
 *
 * @see docs/03-engines/view.md
 */
final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        private string $template,
        private array $data = [],
    ) {
    }

    public function with(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function withData(array $data): self
    {
        $this->data = \array_merge($this->data, $data);
        return $this;
    }

    public function render(): string
    {
        // If template is a file path, load it
        if (\is_file($this->template)) {
            $content = (string) \file_get_contents($this->template);
        } else {
            $content = $this->template;
        }
        // Compile
        $compiler = new TemplateCompiler();
        $compiled = $compiler->compile($content);
        // Render in isolated scope
        \extract($this->data, \EXTR_SKIP);
        \ob_start();
        try {
            \eval('?>' . $compiled);
        } catch (\Throwable $e) {
            \ob_end_clean();
            throw new Exceptions\ViewException("View render failed: " . $e->getMessage(), 0, $e);
        }
        return (string) \ob_get_clean();
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function getName(): string
    {
        return $this->template;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * extends
     */
    public function extends(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }
    /**
     * nest
     */
    public function nest(string $name, string $view, array $data = []): self
    {
        $this->data[$name] = (new View($view, $data))->render();
        return $this;
    }
    /**
     * section
     */
    public function section(string $name, string $content): self
    {
        $this->sections[$name] = $content;
        return $this;
    }
}
