<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config\Exceptions;

use RuntimeException;

/**
 * خطای parse فایل .env.
 */
final class EnvParseException extends RuntimeException
{
    public static function forLine(int $line, string $content): self
    {
        return new self("Failed to parse .env line {$line}: {$content}");
    }
}
