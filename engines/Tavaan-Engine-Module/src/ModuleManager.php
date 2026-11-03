<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module;

/**
 * ModuleManager — پیاده‌سازی مطابق مستندات.
 *
 * @see docs/03-engines/ (YAML machine-readable block)
 */
final class ModuleManager
{
    /**
     * install
     */
    public function install(string $name): bool
    {
        $this->registry->get($name)->enabled = true;
        return true;
    }
    /**
     * uninstall
     */
    public function uninstall(string $name): bool
    {
        $module = $this->registry->get($name);
        $module->enabled = false;
        return true;
    }
    /**
     * enable
     */
    public function enable(string $name): void
    {
        $this->registry->enable($name);
    }
    /**
     * disable
     */
    public function disable(string $name): void
    {
        $this->registry->disable($name);
    }
    /**
     * update
     */
    public function update(array $values): self
    {
        return $this;
    }
    /**
     * validate
     */
    public function validate(string $name): bool
    {
        return $this->registry->has($name);
    }
}
