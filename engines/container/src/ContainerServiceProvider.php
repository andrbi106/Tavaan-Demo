<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container;

use Tavaan\Engine\Container\Contracts\ContainerInterface;

/**
 * ContainerServiceProvider — ثبت خودِ Container در سیستم.
 *
 * در عمل، Container خود را ثبت می‌کند تا قابل Resolve از طریق ContainerInterface باشد.
 */
final class ContainerServiceProvider
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    public function register(): void
    {
        $this->container->instance(ContainerInterface::class, $this->container);
    }

    public function boot(): void
    {
        // No additional boot logic
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [ContainerInterface::class, Container::class];
    }
}
