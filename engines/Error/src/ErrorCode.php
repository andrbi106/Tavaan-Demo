<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

/**
 * ErrorCode — ثابت‌های کد خطا.
 */
final class ErrorCode
{
    public const string GENERIC = 'ERR.GENERIC';
    public const string NOT_FOUND = 'ERR.NOT_FOUND';
    public const string UNAUTHORIZED = 'ERR.UNAUTHORIZED';
    public const string FORBIDDEN = 'ERR.FORBIDDEN';
    public const string VALIDATION = 'ERR.VALIDATION';
    public const string RATE_LIMITED = 'ERR.RATE_LIMITED';
    public const string SERVER_ERROR = 'ERR.SERVER_ERROR';
    public const string MAINTENANCE = 'ERR.MAINTENANCE';
    public const string TIMEOUT = 'ERR.TIMEOUT';
    public const string BAD_REQUEST = 'ERR.BAD_REQUEST';
    public const string CONFLICT = 'ERR.CONFLICT';
    public const string GONE = 'ERR.GONE';
    public const string UNPROCESSABLE = 'ERR.UNPROCESSABLE';
    public const string TOO_MANY_REQUESTS = 'ERR.TOO_MANY_REQUESTS';
}
