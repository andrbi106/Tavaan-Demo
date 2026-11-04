<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Exceptions;

use Tavaan\Core\Exceptions\TavaanException;

use RuntimeException;

/**
 * خطای رمزنگاری.
 */
final class EncryptionException extends TavaanException
{
    public static function forOpenSslError(string $message = ''): self
    {
        $error = \openssl_error_string() ?: 'unknown openssl error';
        return new self($message !== '' ? "{$message}: {$error}" : $error);
    }
}
