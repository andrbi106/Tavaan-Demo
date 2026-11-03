<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat;

use Tavaan\Engine\Compat\Contracts\CompatInterface;
use Tavaan\Engine\Compat\Console\DoctorCommand;

/**
 * CompatServiceProvider — ثبت سرویس‌های موتور Compat.
 *
 * @see docs/03-engines/compat.md
 */
final class CompatServiceProvider
{
    private \Closure $resolver;
    private \Closure $configResolver;
    /** @var list<class-string> */
    private array $commands = [];

    /**
     * @param callable(): CompatInterface $resolver
     * @param callable(): array<string, mixed> $configResolver
     */
    public function __construct(callable $resolver, callable $configResolver)
    {
        $this->resolver = \Closure::fromCallable($resolver);
        $this->configResolver = \Closure::fromCallable($configResolver);
    }

    /**
     * Register singleton instances.
     */
    public function register(): void
    {
        $resolver = $this->resolver;
        $configResolver = $this->configResolver;
        // In a real Tavaan app, this would use the container
        // Here we expose a static accessor for tests
        $GLOBALS['tvn.compat'] = $resolver();
        $GLOBALS['tvn.compat.config'] = $configResolver();
    }

    /**
     * Boot — register commands and facades.
     */
    public function boot(): void
    {
        $this->commands[] = DoctorCommand::class;
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        $list = [CompatInterface::class];
        return \array_merge($list, $this->commands);
    }

    /**
     * Static accessor for tests and minimal usage.
     */
    public static function instance(): CompatInterface
    {
        if (!isset($GLOBALS['tvn.compat'])) {
            $GLOBALS['tvn.compat'] = new Compat([]);
        }
        return $GLOBALS['tvn.compat'];
    }
}
