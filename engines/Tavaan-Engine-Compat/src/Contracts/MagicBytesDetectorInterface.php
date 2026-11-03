<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat\Contracts;

/**
 * قرارداد MagicBytesDetector.
 *
 * @see \Tavaan\Engine\Compat\MagicBytesDetector
 * @api 1.0
 */
interface MagicBytesDetectorInterface
{
    public static function detect(string $head, ?string $hintName = null): ?string;

    public static function detectFromFile(string $path): ?string;

    /**
     * @param resource $stream
     */
    public static function detectFromStream($stream): ?string;
}
