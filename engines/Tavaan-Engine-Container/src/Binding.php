<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container;

/**
 * رکورد readonly یک binding.
 */
final readonly class Binding
{
    public function __construct(
        public string $abstract,
        public \Closure|string|null $concrete,
        public Lifetime $lifetime,
        public bool $shared = false,
    ) {
    }

    public function isShared(): bool
    {
        return $this->lifetime === Lifetime::Singleton
            || $this->lifetime === Lifetime::Scoped
            || $this->lifetime === Lifetime::Instance;
    }
}
