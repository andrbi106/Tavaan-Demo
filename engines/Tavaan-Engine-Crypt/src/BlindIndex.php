<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt;

/**
 * BlindIndex — پیاده‌سازی مطابق مستندات.
 *
 * @see docs/03-engines/ (YAML machine-readable block)
 */
final class BlindIndex
{
    /**
     * compute
     */
    public function compute(string $value, ?string $key = null): string
    {
        $key ??= bin2hex(random_bytes(32));
        return hash_hmac("sha256", $value, $key);
    }
    /**
     * verify
     */
    public function verify(string $value, string $index, ?string $key = null): bool
    {
        $expected = $this->compute($value, $key);
        return hash_equals($expected, $index);
    }
}
