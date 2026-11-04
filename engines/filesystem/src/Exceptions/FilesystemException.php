<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem\Exceptions;

use Tavaan\Core\Exceptions\TavaanException;

use RuntimeException;

/**
 * خطای filesystem.
 */
final class FilesystemException extends TavaanException
{
    public static function forPath(string $path, string $reason): self
    {
        return new self("Filesystem error for path '{$path}': {$reason}");
    }

    public static function pathOutsideRoot(string $path): self
    {
        return new self("Path '{$path}' is outside the filesystem root.");
    }
}
