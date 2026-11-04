<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module;

use Tavaan\Engine\Module\Exceptions\ModuleException;

/**
 * TopologicalSorter — مرتب‌سازی ماژول‌ها بر اساس وابستگی‌ها.
 */
final class TopologicalSorter
{
    /**
     * @param array<string, Module> $modules
     * @return list<string>  ترتیب اجرا
     * @throws ModuleException اگر cycle باشد
     */
    public function sort(array $modules): array
    {
        $sorted = [];
        $visited = [];
        $visiting = [];
        foreach (\array_keys($modules) as $id) {
            $this->visit($id, $modules, $sorted, $visited, $visiting);
        }
        return $sorted;
    }

    private function visit(string $id, array $modules, array &$sorted, array &$visited, array &$visiting): void
    {
        if (isset($visited[$id])) return;
        if (isset($visiting[$id])) {
            throw ModuleException::circularDependency($id);
        }
        if (!isset($modules[$id])) return;
        $visiting[$id] = true;
        foreach ($modules[$id]->requires() as $dep) {
            $this->visit($dep, $modules, $sorted, $visited, $visiting);
        }
        unset($visiting[$id]);
        $visited[$id] = true;
        $sorted[] = $id;
    }
}
