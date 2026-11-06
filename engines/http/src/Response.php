<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http;

/**
 * Response — پاسخ HTTP immutable.
 *
 * @see docs/03-engines/http.md (DOC-039)
 */
final class Response
{
    public HeaderBag $headers;
    public ?string $content;

    public function __construct(
        public int $status = 200,
        string $content = '',
        array $headers = [],
    ) {
        $this->headers = new HeaderBag($headers);
        $this->content = $content;
    }

    public function getStatus(): int { return $this->status; }
    public function getContent(): string { return $this->content ?? ''; }

    public function isSuccessful(): bool { return $this->status >= 200 && $this->status < 300; }
    public function isClientError(): bool { return $this->status >= 400 && $this->status < 500; }
    public function isServerError(): bool { return $this->status >= 500; }
    public function isOk(): bool { return $this->status === 200; }
    public function isNotFound(): bool { return $this->status === 404; }
    public function isForbidden(): bool { return $this->status === 403; }
    public function isUnauthorized(): bool { return $this->status === 401; }

    public function setHeader(string $name, string $value): self
    {
        $this->headers->set($name, $value);
        return $this;
    }

    public function getHeader(string $name, ?string $default = null): ?string
    {
        return $this->headers->get($name, $default);
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function send(): void
    {
        if (!\headers_sent()) {
            \http_response_code($this->status);
            foreach ($this->headers->all() as $name => $value) {
                $originalName = \str_replace(' ', '-', \ucwords(\str_replace('-', ' ', $name)));
                \header("{$originalName}: {$value}");
            }
        }
        echo $this->getContent();
    }

    // === Static factories ===

    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        $content = (string) \json_encode($data, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        return new self($status, $content, \array_merge(['Content-Type' => 'application/json; charset=utf-8'], $headers));
    }

    public static function text(string $content, int $status = 200, array $headers = []): self
    {
        return new self($status, $content, \array_merge(['Content-Type' => 'text/plain; charset=utf-8'], $headers));
    }

    public static function html(string $content, int $status = 200, array $headers = []): self
    {
        return new self($status, $content, \array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers));
    }

    public static function redirect(string $url, int $status = 302, array $headers = []): self
    {
        return new self($status, '', \array_merge(['Location' => $url], $headers));
    }

    public static function notFound(string $message = 'Not Found', array $headers = []): self
    {
        return new self(404, $message, $headers);
    }

    public static function forbidden(string $message = 'Forbidden', array $headers = []): self
    {
        return new self(403, $message, $headers);
    }

    public static function unauthorized(string $message = 'Unauthorized', array $headers = []): self
    {
        return new self(401, $message, $headers);
    }

    public static function badRequest(string $message = 'Bad Request', array $headers = []): self
    {
        return new self(400, $message, $headers);
    }

    public static function serverError(string $message = 'Internal Server Error', array $headers = []): self
    {
        return new self(500, $message, $headers);
    }

    public static function noContent(): self
    {
        return new self(204, '');
    }

    public function getStatusText(): string
    {
        return match ($this->status) {
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
            default => 'Unknown',
        };
    }

    /**
     * body
     */
    public function body(): string
    {
        return $this->content ?? "";
    }
    /**
     * cookie
     */
    public function cookie(string $key, mixed $default = null): string
    {
        return $this->cookies->get($key, $default);
    }
    /**
     * header
     */
    public function header(string $name, ?string $default = null): string
    {
        return $this->headers->get($name, $default);
    }
    /**
     * isRedirect
     */
    public function isRedirect(): bool
    {
        return $this->status >= 300 && $this->status < 400;
    }
    /**
     * statusCode
     */
    public function statusCode(): int
    {
        return $this->status;
    }
    /**
     * withBody
     */
    public function withBody(string $body): self
    {
        $clone = clone $this; return $clone;
    }
    /**
     * withHeader
     */
    public function withHeader(string $name, string $value): self
    {
        $clone = clone $this; return $clone;
    }
    /**
     * withHeaders
     */
    public function withHeaders(array $headers): self
    {
        $clone = clone $this; foreach ($headers as $k => $v) { $clone->headers->set($k, $v); } return $clone;
    }
    /**
     * withJson
     */
    public function withJson(mixed $data, int $status = 200): self
    {
        $content = json_encode($data, JSON_UNESCAPED_UNICODE);
        return new self($status, $content ?: "{}", ["Content-Type" => "application/json"]);
    }
    /**
     * withStatus
     */
    public function withStatus(int $code): self
    {
        $clone = clone $this; return $clone;
    }
}
