<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container\Exceptions;

use RuntimeException;

/**
 * وقتی یک وابستگی قابل Resolve نیست.
 */
final class BindingResolutionException extends RuntimeException
{
    public static function forAbstract(string $abstract): self
    {
        return new self("No binding registered for: {$abstract}");
    }

    public static function forParameter(string $class, string $parameter): self
    {
        return new self("Cannot resolve parameter ${$parameter} of {$class}.");
    }

    public static function cyclic(string $abstract): self
    {
        return new self("Cyclic dependency detected while resolving: {$abstract}");
    }
}
