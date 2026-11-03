<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

/**
 * HttpException — استثنا با HTTP status code و refCode.
 */
class HttpException extends \RuntimeException
{
    public function __construct(
        public readonly int $statusCode,
        string $message = '',
        public readonly string $errorCode = ErrorCode::GENERIC,
        public readonly array $context = [],
        ?\Throwable $previous = null,
        public readonly ?string $refCode = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public static function notFound(string $message = 'Not Found', array $context = []): self
    {
        return new self(404, $message, ErrorCode::NOT_FOUND, $context);
    }

    public static function unauthorized(string $message = 'Unauthorized', array $context = []): self
    {
        return new self(401, $message, ErrorCode::UNAUTHORIZED, $context);
    }

    public static function forbidden(string $message = 'Forbidden', array $context = []): self
    {
        return new self(403, $message, ErrorCode::FORBIDDEN, $context);
    }

    public static function badRequest(string $message = 'Bad Request', array $context = []): self
    {
        return new self(400, $message, ErrorCode::BAD_REQUEST, $context);
    }

    public static function validation(array $errors, string $message = 'Validation Failed'): self
    {
        return new self(422, $message, ErrorCode::VALIDATION, ['errors' => $errors]);
    }

    public static function serverError(string $message = 'Internal Server Error', array $context = []): self
    {
        return new self(500, $message, ErrorCode::SERVER_ERROR, $context);
    }

    public static function rateLimited(int $retryAfter = 60, array $context = []): self
    {
        return new self(429, 'Too Many Requests', ErrorCode::TOO_MANY_REQUESTS,
            \array_merge(['retry_after' => $retryAfter], $context));
    }

    public static function maintenance(array $context = []): self
    {
        return new self(503, 'Maintenance Mode', ErrorCode::MAINTENANCE, $context);
    }
}
