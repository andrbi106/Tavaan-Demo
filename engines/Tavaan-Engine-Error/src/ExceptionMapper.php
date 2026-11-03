<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

/**
 * ExceptionMapper — نگاشت Throwable → HttpException.
 */
final class ExceptionMapper
{
    public function map(\Throwable $e): HttpException
    {
        if ($e instanceof HttpException) {
            return $e;
        }
        return match (true) {
            $e instanceof \InvalidArgumentException => HttpException::badRequest($e->getMessage()),
            $e instanceof \RuntimeException => HttpException::serverError($e->getMessage()),
            $e instanceof \TypeError => HttpException::serverError('Type error: ' . $e->getMessage()),
            default => HttpException::serverError($e->getMessage()),
        };
    }
}
