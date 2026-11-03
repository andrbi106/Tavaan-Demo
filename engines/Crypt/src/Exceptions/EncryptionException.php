<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Exceptions;

use RuntimeException;

/**
 * خطای رمزنگاری.
 */
final class EncryptionException extends RuntimeException
{
    public static function forOpenSslError(string $message = ''): self
    {
        $error = \openssl_error_string() ?: 'unknown openssl error';
        return new self($message !== '' ? "{$message}: {$error}" : $error);
    }
}
