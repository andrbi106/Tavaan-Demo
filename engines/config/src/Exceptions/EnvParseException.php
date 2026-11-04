<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config\Exceptions;

use Tavaan\Core\Exceptions\TavaanException;

use RuntimeException;

/**
 * خطای parse فایل .env.
 */
final class EnvParseException extends TavaanException
{
    public static function forLine(int $line, string $content): self
    {
        return new self("Failed to parse .env line {$line}: {$content}");
    }
}
