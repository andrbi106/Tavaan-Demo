<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Exceptions;

use Tavaan\Core\Exceptions\TavaanException;

use RuntimeException;

/**
 * خطای رمزگشایی (مثل payload نامعتبر، tamper detection).
 */
final class DecryptException extends TavaanException
{
    public static function invalidFormat(): self
    {
        return new self('Invalid encrypted payload format. Expected v<N>:<iv>:<ct>:<tag>.');
    }

    public static function invalidPayload(): self
    {
        return new self('Invalid encrypted payload (base64 decode failed or tag length wrong).');
    }

    public static function decryptFailed(): self
    {
        return new self('Decryption failed (possibly tampered data).');
    }

    public static function keyMissing(int $version): self
    {
        return new self("Key version {$version} not found in KeyRing.");
    }

    public static function invalidJson(): self
    {
        return new self('Decrypted data is not valid JSON.');
    }
}
