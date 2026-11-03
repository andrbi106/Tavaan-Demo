<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media\Exceptions;

use RuntimeException;

final class MediaException extends RuntimeException
{
    public static function uploadFailed(string $reason): self
    {
        return new self("Upload failed: {$reason}");
    }

    public static function invalidMime(string $mime): self
    {
        return new self("Invalid MIME type: {$mime}");
    }

    public static function extensionMismatch(string $ext, string $mime): self
    {
        return new self("Extension '{$ext}' does not match MIME '{$mime}'.");
    }

    public static function fileTooLarge(int $size, int $max): self
    {
        return new self("File too large: {$size} bytes (max: {$max}).");
    }

    public static function processingFailed(string $reason): self
    {
        return new self("Image processing failed: {$reason}");
    }
}
