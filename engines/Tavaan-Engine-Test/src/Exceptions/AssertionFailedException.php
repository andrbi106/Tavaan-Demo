<?php

declare(strict_types=1);

namespace Tavaan\Testing\Exceptions;

use RuntimeException;

/**
 * خطای Assertion — وقتی یک assertion شکست می‌خورد.
 */
final class AssertionFailedException extends RuntimeException
{
    public static function forAssertion(string $assertion, string $message): self
    {
        return new self("[{$assertion}] {$message}");
    }
}
