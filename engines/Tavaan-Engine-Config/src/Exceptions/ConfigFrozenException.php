<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config\Exceptions;

use RuntimeException;

/**
 * وقتی تلاش می‌شود روی یک Config Frozen بنویسیم.
 */
final class ConfigFrozenException extends RuntimeException
{
    public function __construct(string $message = 'Configuration is frozen and cannot be modified.')
    {
        parent::__construct($message);
    }
}
