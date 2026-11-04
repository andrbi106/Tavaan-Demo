<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module;

use Tavaan\Engine\Module\Exceptions\ModuleException;

/**
 * ModuleRegistry — ثبت، جست‌وجو و مدیریت ماژول‌ها.
 */
final class ModuleRegistry
{
    /**
     * @var array<string, Module>
     */
    private array $modules = [];

    public function __construct(
        private readonly ManifestParser $parser = new ManifestParser(),
        private readonly TopologicalSorter $sorter = new TopologicalSorter(),
    ) {
    }

    /**
     * کشف همه‌ی ماژول‌ها از یک دایرکتوری.
     *
     * @param string $baseDir مسیر modules/
     */
    public function discover(string $baseDir): void
    {
        if (!\is_dir($baseDir)) return;
        foreach (\scandir($baseDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $manifestPath = "{$baseDir}/{$entry}/module.json";
            if (\file_exists($manifestPath)) {
                try {
                    $module = $this->parser->parse($manifestPath);
                    $this->modules[$module->id] = $module;
                } catch (ModuleException $e) {
                    // Skip invalid module
                    continue;
                }
            }
        }
    }

    public function register(Module $module): void
    {
        $this->modules[$module->id] = $module;
    }

    public function get(string $id): Module
    {
        if (!isset($this->modules[$id])) {
            throw ModuleException::notFound($id);
        }
        return $this->modules[$id];
    }

    public function all(): array
    {
        return \array_values($this->modules);
    }

    public function enabled(): array
    {
        return \array_values(\array_filter($this->modules, fn(Module $m) => $m->enabled));
    }

    public function enable(string $id): void
    {
        $this->get($id)->enabled = true;
    }

    public function disable(string $id): void
    {
        $this->get($id)->enabled = false;
    }

    public function enabled_check(string $id): bool
    {
        try {
            return $this->get($id)->enabled;
        } catch (ModuleException) {
            return false;
        }
    }

    public function sorted(): array
    {
        $sorted = $this->sorter->sort($this->modules);
        return \array_map(fn($id) => $this->modules[$id], $sorted);
    }

    /**
     * لیست وابستگی‌های یک ماژول.
     *
     * @return list<string>
     */
    public function dependencies(string $id): array
    {
        return $this->get($id)->requires();
    }

    /**
     * لیست ماژول‌هایی که به این ماژول وابسته‌اند.
     *
     * @return list<string>
     */
    public function dependentsOf(string $id): array
    {
        $result = [];
        foreach ($this->modules as $mod) {
            if (\in_array($id, $mod->requires(), true)) {
                $result[] = $mod->id;
            }
        }
        return $result;
    }

    public function has(string $id): bool
    {
        return isset($this->modules[$id]);
    }
}
