<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container\Exceptions;

use RuntimeException;

/**
 * وقتی یک کلاس قابل Resolve نیست.
 */
final class NotInstantiableException extends RuntimeException
{
    public static function forClass(string $class): self
    {
        return new self("Cannot instantiate: {$class} (interface/abstract without binding).");
    }

    public static function forCtor(string $class): self
    {
        return new self("Cannot resolve constructor parameters for: {$class}.");
    }
}
