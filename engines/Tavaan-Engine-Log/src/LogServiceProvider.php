<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log;

use Tavaan\Engine\Log\Contracts\LoggerInterface;

/**
 * LogServiceProvider — ثبت موتور Log.
 */
final class LogServiceProvider
{
    private ?LogManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->manager = new LogManager($this->config);
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
        return [LoggerInterface::class, LogManager::class];
    }

    public function getManager(): LogManager
    {
        if ($this->manager === null) {
            $this->register();
        }
        return $this->manager;
    }
}
