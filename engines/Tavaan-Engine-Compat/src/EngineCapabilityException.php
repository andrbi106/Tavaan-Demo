<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat;

use RuntimeException;

/**
 * Exception برای زمانی که یک قابلیت مورد نیاز موجود نیست.
 */
final class EngineCapabilityException extends RuntimeException
{
    public static function forExtension(string $extension, string $capability): self
    {
        return new self(
            "افزونهٔ '{$extension}' برای قابلیت '{$capability}' مورد نیاز است اما بارگذاری نشده است. "
            . 'از Fallback یا نصب افزونه استفاده کنید.'
        );
    }
}
