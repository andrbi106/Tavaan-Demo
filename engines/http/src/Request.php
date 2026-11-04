<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http;

/**
 * ParameterBag — کیسه‌ای از پارامترها (query, post, attributes).
 */
final class ParameterBag
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private array $parameters = [],
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->parameters);
    }

    public function remove(string $key): void
    {
        unset($this->parameters[$key]);
    }

    public function all(): array
    {
        return $this->parameters;
    }

    public function count(): int
    {
        return \count($this->parameters);
    }

    public function string(string $key, string $default = ''): string
    {
        $v = $this->get($key);
        return \is_string($v) ? $v : (\is_scalar($v) ? (string) $v : $default);
    }

    public function int(string $key, int $default = 0): int
    {
        $v = $this->get($key);
        return \is_numeric($v) ? (int) $v : $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $v = $this->get($key);
        if (\is_bool($v)) return $v;
        if (\is_string($v)) return \in_array(\strtolower($v), ['true', '1', 'yes', 'on'], true);
        if (\is_int($v)) return $v !== 0;
        return $default;
    }
}

/**
 * HeaderBag — headerها case-insensitive.
 */
final class HeaderBag
{
    /**
     * @param array<string, list<string>|string> $headers
     */
    public function __construct(
        private array $headers = [],
    ) {
        // Normalize to lowercase keys
        $normalized = [];
        foreach ($headers as $k => $v) {
            $normalized[\strtolower($k)] = $v;
        }
        $this->headers = $normalized;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $k = \strtolower($key);
        if (!isset($this->headers[$k])) return $default;
        $v = $this->headers[$k];
        return \is_array($v) ? ($v[0] ?? null) : $v;
    }

    public function set(string $key, string $value): void
    {
        $this->headers[\strtolower($key)] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->headers[\strtolower($key)]);
    }

    public function remove(string $key): void
    {
        unset($this->headers[\strtolower($key)]);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        $result = [];
        foreach ($this->headers as $k => $v) {
            $result[$k] = \is_array($v) ? \implode(', ', $v) : $v;
        }
        return $result;
    }
}

/**
 * Uri — پارس و ساخت URI (RFC 3986).
 */
final class Uri
{
    private string $scheme = '';
    private string $host = '';
    private ?int $port = null;
    private string $path = '/';
    private string $query = '';
    private string $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri !== '') $this->parse($uri);
    }

    private function parse(string $uri): void
    {
        $parts = \parse_url($uri);
        if ($parts === false) return;
        $this->scheme = $parts['scheme'] ?? '';
        $this->host = $parts['host'] ?? '';
        $this->port = $parts['port'] ?? null;
        $this->path = $parts['path'] ?? '/';
        $this->query = $parts['query'] ?? '';
        $this->fragment = $parts['fragment'] ?? '';
    }

    public function getScheme(): string { return $this->scheme; }
    public function getHost(): string { return $this->host; }
    public function getPort(): ?int { return $this->port; }
    public function getPath(): string { return $this->path; }
    public function getQuery(): string { return $this->query; }
    public function getFragment(): string { return $this->fragment; }

    public function __toString(): string
    {
        $uri = '';
        if ($this->scheme !== '') $uri .= $this->scheme . '://';
        $uri .= $this->host;
        if ($this->port !== null) $uri .= ':' . $this->port;
        $uri .= $this->path;
        if ($this->query !== '') $uri .= '?' . $this->query;
        if ($this->fragment !== '') $uri .= '#' . $this->fragment;
        return $uri;
    }
}

/**
 * Request — درخواست HTTP immutable.
 *
 * @see docs/03-engines/http.md (DOC-039)
 */
final class Request
{
    public readonly ParameterBag $query;
    public readonly ParameterBag $request;
    public readonly ParameterBag $attributes;
    public readonly HeaderBag $headers;
    public readonly ParameterBag $cookies;

    private ?string $content = null;

    public function __construct(
        public readonly string $method = 'GET',
        public readonly Uri $uri = new Uri(),
        array $query = [],
        array $post = [],
        array $cookies = [],
        array $headers = [],
        ?string $content = null,
    ) {
        $this->query = new ParameterBag($query);
        $this->request = new ParameterBag($post);
        $this->attributes = new ParameterBag();
        $this->headers = new HeaderBag($headers);
        $this->cookies = new ParameterBag($cookies);
        $this->content = $content;
    }

    public static function create(string $uri, string $method = 'GET', array $params = [], array $headers = [], ?string $content = null): self
    {
        $parsed = new Uri($uri);
        $query = [];
        if ($parsed->getQuery() !== '') {
            \parse_str($parsed->getQuery(), $query);
        }
        return new self(
            method: $method,
            uri: $parsed,
            query: $query,
            post: \strtoupper($method) === 'GET' ? [] : $params,
            headers: $headers,
            content: $content,
        );
    }

    public static function capture(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = new Uri($_SERVER['REQUEST_URI'] ?? '/');
        $headers = self::extractHeaders($_SERVER);
        return new self(
            method: $method,
            uri: $uri,
            query: $_GET,
            post: $_POST,
            cookies: $_COOKIE,
            headers: $headers,
            content: \file_get_contents('php://input') ?: null,
        );
    }

    /**
     * @param array<string, mixed> $server
     * @return array<string, string>
     */
    private static function extractHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $k => $v) {
            if (\str_starts_with($k, 'HTTP_')) {
                $name = \str_replace('_', '-', \substr($k, 5));
                $headers[$name] = (string) $v;
            }
        }
        if (isset($server['CONTENT_TYPE'])) $headers['Content-Type'] = (string) $server['CONTENT_TYPE'];
        if (isset($server['CONTENT_LENGTH'])) $headers['Content-Length'] = (string) $server['CONTENT_LENGTH'];
        return $headers;
    }

    public function getMethod(): string { return $this->method; }
    public function getUri(): Uri { return $this->uri; }
    public function getPath(): string { return $this->uri->getPath(); }
    public function isMethod(string $method): bool { return \strcasecmp($this->method, $method) === 0; }
    public function isGet(): bool { return $this->isMethod('GET'); }
    public function isPost(): bool { return $this->isMethod('POST'); }
    public function isPut(): bool { return $this->isMethod('PUT'); }
    public function isDelete(): bool { return $this->isMethod('DELETE'); }

    public function getContent(): string
    {
        if ($this->content === null) {
            $this->content = '';
        }
        return $this->content;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        $data = \json_decode($this->getContent(), true);
        if (!\is_array($data)) return $default;
        if ($key === null) return $data;
        $segments = \explode('.', $key);
        $value = $data;
        foreach ($segments as $seg) {
            if (!\is_array($value) || !\array_key_exists($seg, $value)) return $default;
            $value = $value[$seg];
        }
        return $value;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->query->get($key, $this->request->get($key, $default));
    }

    public function all(): array
    {
        return \array_merge($this->query->all(), $this->request->all());
    }

    public function header(string $key, ?string $default = null): ?string
    {
        return $this->headers->get($key, $default);
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies->get($key, $default);
    }

    public function getAcceptableContentTypes(): array
    {
        $accept = $this->headers->get('Accept', '');
        if ($accept === '') return [];
        $types = \array_map(\trim(...), \explode(',', $accept));
        return $types;
    }

    public function expectsJson(): bool
    {
        $accept = $this->headers->get('Accept', '');
        return $accept !== null && \str_contains($accept, 'application/json');
    }

    /**
     * attribute
     */
    public function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes->get($key, $default);
    }
    /**
     * except
     */
    public function except(array $keys)
    {
        return array_diff_key($this->getAttributes(), array_flip($keys));
    }
    /**
     * file
     */
    public function file(string $key): mixed
    {
        return $this->files[$key] ?? null;
    }
    /**
     * files
     */
    public function files(): array
    {
        return $this->files ?? [];
    }
    /**
     * fullUrl
     */
    public function fullUrl(): string
    {
        return (string) $this->uri;
    }
    /**
     * ip
     */
    public function ip(): string
    {
        return $this->headers->get("X-Forwarded-For") ?? $this->headers->get("Client-IP") ?? "127.0.0.1";
    }
    /**
     * isSecure
     */
    public function isSecure(): bool
    {
        return $this->uri->getScheme() === "https";
    }
    /**
     * method
     */
    public function method(): string
    {
        return $this->method;
    }
    /**
     * only
     */
    public function only(array $keys)
    {
        return array_intersect_key($this->getAttributes(), array_flip($keys));
    }
    /**
     * path
     */
    public function path(): string
    {
        return $this->uri->getPath();
    }
    /**
     * post
     */
    public function post(?string $key = null, mixed $default = null): string
    {
        return $key === null ? $this->request->all() : $this->request->get($key, $default);
    }
    /**
     * query
     */
    public function query(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? $this->query->all() : $this->query->get($key, $default);
    }
    /**
     * withAttribute
     */
    public function withAttribute(string $name, mixed $value): self
    {
        $clone = clone $this; return $clone;
    }
    /**
     * withMethod
     */
    public function withMethod(string $method): self
    {
        $clone = clone $this; return $clone;
    }
    /**
     * withQuery
     */
    public function withQuery(array $query): self
    {
        $clone = clone $this; $clone->query = new ParameterBag($query); return $clone;
    }
    /**
     * withRoute
     */
    public function withRoute(mixed $route): self
    {
        $this->attributes->set("_route", $route); return $this;
    }
}
