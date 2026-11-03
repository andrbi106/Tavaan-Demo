<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container\Console;

use Tavaan\Engine\Container\Container;

/**
 * Command `container:graph` — نمایش گراف وابستگی‌های ثبت‌شده.
 */
final class ContainerGraphCommand
{
    public function __construct(
        private readonly Container $container,
    ) {
    }

    public function name(): string
    {
        return 'container:graph';
    }

    public function description(): string
    {
        return 'نمایش تمام bindingهای ثبت‌شده در Container.';
    }

    public function handle(array $args = []): int
    {
        $bindings = $this->container->bindings();
        \sort($bindings);
        echo "Container Bindings (" . \count($bindings) . "):
";
        foreach ($bindings as $abstract) {
            echo "  - {$abstract}
";
        }
        return 0;
    }
}
