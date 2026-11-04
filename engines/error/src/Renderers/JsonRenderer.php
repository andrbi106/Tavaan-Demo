<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error\Renderers;

use Tavaan\Engine\Error\HttpException;

/**
 * JsonRenderer — خروجی خطا به‌صورت JSON (RFC 9457).
 */
final class JsonRenderer
{
    public function render(HttpException $e, bool $debug = false): string
    {
        $body = [
            'type' => 'about:blank',
            'title' => $this->titleFor($e->statusCode),
            'status' => $e->statusCode,
            'detail' => $e->getMessage(),
            'code' => $e->errorCode,
            'ref' => $e->refCode,
        ];
        if (!empty($e->context)) {
            $body['context'] = $e->context;
        }
        if ($debug && $e->getPrevious() !== null) {
            $body['previous'] = [
                'class' => \get_class($e->getPrevious()),
                'message' => $e->getPrevious()->getMessage(),
                'file' => $e->getPrevious()->getFile(),
                'line' => $e->getPrevious()->getLine(),
            ];
        }
        return (string) \json_encode($body, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE);
    }

    private function titleFor(int $status): string
    {
        return match ($status) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            410 => 'Gone',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
            default => 'Error',
        };
    }
}
