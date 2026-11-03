<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module;

/**
 * ModuleRunner — پیاده‌سازی مطابق مستندات.
 *
 * @see docs/03-engines/ (YAML machine-readable block)
 */
final class ModuleRunner
{
    /**
     * run
     */
    public function run(string $uri, array $params = []): mixed
    {
        $parts = explode("/", $uri, 2);
        $module = $parts[0] ?? "";
        $action = $parts[1] ?? "index";
        $mod = $this->registry->get("tavaan." . $module);
        if ($mod === null) return null;
        return ["module" => $module, "action" => $action, "params" => $params];
    }
}
