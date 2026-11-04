<?php
declare(strict_types=1);
namespace Tavaan\Core\Container;

/**
 * Value object برای یک binding در کانتینر.
 */
final class Binding
{
    public function __construct(
        public readonly \Closure|string|null $concrete,
        public readonly Lifetime $lifetime = Lifetime::Transient,
        public readonly bool $resolved = false,
    ) {}
}
