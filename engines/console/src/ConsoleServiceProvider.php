<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console;

/**
 * ConsoleServiceProvider — ثبت موتور Console.
 */
final class ConsoleServiceProvider
{
    private ?Application $app = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->app = new Application();
        // Auto-register a 'list' command
        $this->app->add(new ListCommand($this->app));
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
        return [Application::class, Command::class];
    }

    public function getApplication(): Application
    {
        if ($this->app === null) {
            $this->register();
        }
        return $this->app;
    }
}
