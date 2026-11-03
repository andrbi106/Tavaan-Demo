<?php

declare(strict_types=1);

namespace Tavaan\Testing\Mock;

/**
 * Mock — ساخت mock ساده بدون eval یا external library.
 *
 * @see docs/03-engines/test.md §4.1
 */
final class Mock
{
    /**
     * @param class-string $interface
     */
    public static function of(string $interface): MockBuilder
    {
        return new MockBuilder($interface);
    }
}
