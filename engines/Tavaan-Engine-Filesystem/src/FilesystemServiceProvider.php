<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem;

use Tavaan\Engine\Filesystem\Contracts\FilesystemInterface;

/**
 * FilesystemServiceProvider — ثبت موتور Filesystem.
 */
final class FilesystemServiceProvider
{
    private ?FilesystemManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->manager = new FilesystemManager($this->config);
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
        return [FilesystemInterface::class, FilesystemManager::class];
    }

    public function getManager(): FilesystemManager
    {
        if ($this->manager === null) {
            $this->register();
        }
        return $this->manager;
    }
}
