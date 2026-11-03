<?php

declare(strict_types=1);

namespace Tavaan\Engine\Module;

/**
 * ModuleServiceProvider — ثبت موتور Module.
 */
final class ModuleServiceProvider
{
    private ?ModuleRegistry $registry = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->registry = new ModuleRegistry();
        $moduleDir = $this->config['path'] ?? 'modules';
        $this->registry->discover($moduleDir);
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [ModuleRegistry::class, Module::class];
    }

    public function getRegistry(): ModuleRegistry
    {
        if ($this->registry === null) {
            $this->register();
        }
        return $this->registry;
    }
}
