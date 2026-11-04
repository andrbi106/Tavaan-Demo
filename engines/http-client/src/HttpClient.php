<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient;

/**
 * HttpClient — facade برای ارسال درخواست HTTP.
 *
 * @see docs/03-engines/http-client.md
 */
final class HttpClient
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public static function new(array $config = []): self
    {
        return new self($config);
    }

    public function request(): PendingRequest
    {
        $req = new PendingRequest();
        // Apply default config
        if (isset($this->config['timeout'])) $req->timeout((int) $this->config['timeout']);
        if (isset($this->config['default_headers'])) $req->withHeaders($this->config['default_headers']);
        return $req;
    }

    public function get(string $url): Response
    {
        return $this->request()->get($url);
    }

    public function post(string $url, mixed $data = null): Response
    {
        return $this->request()->post($url, $data);
    }

    public function put(string $url, mixed $data = null): Response
    {
        return $this->request()->put($url, $data);
    }

    public function patch(string $url, mixed $data = null): Response
    {
        return $this->request()->patch($url, $data);
    }

    public function delete(string $url, mixed $data = null): Response
    {
        return $this->request()->delete($url, $data);
    }

    public function head(string $url): Response
    {
        return $this->request()->head($url);
    }

    /**
     * @param callable(\Tavaan\Engine\HttpClient\Pool): void $callback
     * @return array<string, Response>
     */
    public function pool(callable $callback): array
    {
        $pool = new Pool();
        $callback($pool);
        return $pool->execute();
    }
}
