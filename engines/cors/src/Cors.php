<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Cors;

/**
 * CorsMiddleware — مدیریت Cross-Origin Resource Sharing.
 *
 * @see docs/03-engines/cors.md (DOC-046)
 */
final class CorsMiddleware
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    /**
     * @param array<string, mixed> $request
     * @param callable $next
     * @return array<string, mixed>
     */
    public function handle(array $request, callable $next): array
    {
        $method = $request['method'] ?? 'GET';
        $isPreflight = $method === 'OPTIONS' && isset($request['headers']['Access-Control-Request-Method']);
        if ($isPreflight) {
            return $this->handlePreflight($request);
        }
        $response = $next($request);
        return $this->addCorsHeaders($request, $response);
    }

    /**
     * @param array<string, mixed> $request
     * @return array<string, mixed>
     */
    private function handlePreflight(array $request): array
    {
        $origin = $request['headers']['Origin'] ?? null;
        $method = $request['headers']['Access-Control-Request-Method'] ?? '';
        $headers = $request['headers']['Access-Control-Request-Headers'] ?? '';
        if ($origin === null || !$this->isAllowed($origin)) {
            return ['status' => 403, 'body' => 'CORS: Origin not allowed', 'headers' => []];
        }
        $allowedMethods = $this->config['allowed_methods'] ?? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        $allowedHeaders = $this->config['allowed_headers'] ?? ['Content-Type', 'Authorization', 'X-Requested-With'];
        $responseHeaders = [
            'Access-Control-Allow-Origin' => $this->reflectOrigin($origin),
            'Access-Control-Allow-Methods' => \is_array($allowedMethods) ? \implode(', ', $allowedMethods) : $allowedMethods,
            'Access-Control-Allow-Headers' => \is_array($allowedHeaders) ? \implode(', ', $allowedHeaders) : $allowedHeaders,
            'Access-Control-Allow-Credentials' => ($this->config['supports_credentials'] ?? false) ? 'true' : 'false',
            'Access-Control-Max-Age' => (string) ($this->config['max_age'] ?? 86400),
            'Vary' => 'Origin',
        ];
        return ['status' => 204, 'body' => '', 'headers' => $responseHeaders];
    }

    /**
     * @param array<string, mixed> $request
     * @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    private function addCorsHeaders(array $request, array $response): array
    {
        $origin = $request['headers']['Origin'] ?? null;
        if ($origin === null || !$this->isAllowed($origin)) {
            return $response;
        }
        $response['headers']['Access-Control-Allow-Origin'] = $this->reflectOrigin($origin);
        $response['headers']['Vary'] = 'Origin';
        if ($this->config['supports_credentials'] ?? false) {
            $response['headers']['Access-Control-Allow-Credentials'] = 'true';
        }
        $exposedHeaders = $this->config['exposed_headers'] ?? [];
        if (!empty($exposedHeaders)) {
            $response['headers']['Access-Control-Expose-Headers'] = \is_array($exposedHeaders) ? \implode(', ', $exposedHeaders) : $exposedHeaders;
        }
        return $response;
    }

    private function isAllowed(string $origin): bool
    {
        $allowedOrigins = $this->config['allowed_origins'] ?? ['*'];
        if ($allowedOrigins === ['*']) return true;
        foreach ($allowedOrigins as $allowed) {
            if ($allowed === $origin) return true;
            if (\str_starts_with($allowed, '*.')) {
                $suffix = \substr($allowed, 1);
                if (\str_ends_with($origin, $suffix)) return true;
            }
        }
        return false;
    }

    private function reflectOrigin(string $origin): string
    {
        if (($this->config['allowed_origins'] ?? ['*']) === ['*']) return '*';
        return $origin;
    }
}

/**
 * CorsServiceProvider.
 */
final class CorsServiceProvider
{
    private ?CorsMiddleware $middleware = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->middleware = new CorsMiddleware($this->config);
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [CorsMiddleware::class];
    }

    public function getMiddleware(): CorsMiddleware
    {
        if ($this->middleware === null) $this->register();
        return $this->middleware;
    }
}
