<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient;

/**
 * Response — پاسخ HTTP.
 */
final class Response
{
    /**
     * @param int $status
     * @param string $body
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly int $status,
        public readonly string $body,
        public readonly array $headers = [],
        private mixed $decoded = null,
    ) {
    }

    public function successful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function failed(): bool
    {
        return !$this->successful();
    }

    public function clientError(): bool
    {
        return $this->status >= 400 && $this->status < 500;
    }

    public function serverError(): bool
    {
        return $this->status >= 500;
    }

    public function header(string $name, ?string $default = null): ?string
    {
        foreach ($this->headers as $k => $v) {
            if (\strtolower($k) === \strtolower($name)) return $v;
        }
        return $default;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->decoded === null) {
            $this->decoded = \json_decode($this->body, true);
        }
        if (!\is_array($this->decoded)) return $default;
        if ($key === null) return $this->decoded;
        $segments = \explode('.', $key);
        $value = $this->decoded;
        foreach ($segments as $seg) {
            if (!\is_array($value) || !\array_key_exists($seg, $value)) return $default;
            $value = $value[$seg];
        }
        return $value;
    }

    public function throw(): self
    {
        if ($this->failed()) {
            throw new Exceptions\HttpClientException("HTTP {$this->status}: {$this->body}");
        }
        return $this;
    }

    public function throwIf(callable $callback): self
    {
        if ($callback($this)) {
            throw new Exceptions\HttpClientException("HTTP {$this->status}: {$this->body}");
        }
        return $this;
    }
}
