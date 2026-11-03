<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient;

final class Factory
{
    private PendingRequest $pending;

    public function __construct(private readonly array $config = [])
    {
        $this->pending = new PendingRequest();
    }

    public function get(string $url): Response
    {
        return $this->pending->get($url);
    }

    public function post(string $url, mixed $data = null): Response
    {
        return $this->pending->post($url, $data);
    }

    public function put(string $url, mixed $data = null): Response
    {
        return $this->pending->put($url, $data);
    }

    public function patch(string $url, mixed $data = null): Response
    {
        return $this->pending->patch($url, $data);
    }

    public function delete(string $url, mixed $data = null): Response
    {
        return $this->pending->delete($url, $data);
    }

    public function withHeaders(array $headers): self
    {
        $this->pending = $this->pending->withHeaders($headers);
        return $this;
    }

    public function withToken(string $token, string $type = 'Bearer'): self
    {
        $this->pending->withToken($token, $type);
        return $this;
    }

    public function asJson(): self
    {
        $this->pending->asJson();
        return $this;
    }

    public function asForm(): self
    {
        $this->pending->asForm();
        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->pending->timeout($seconds);
        return $this;
    }

    public function retry(int $times, int $delayMs = 100): self
    {
        $this->pending->retry($times, $delayMs);
        return $this;
    }

    public function pool(callable $callback): array
    {
        $pool = new Pool();
        $callback($pool);
        return $pool->execute();
    }

    public function fake(array $urls = []): self
    {
        return $this;
    }

    public function assertSent(callable $callback): self
    {
        return $this;
    }

    public function withQueryParameters(array $params): self
    {
        $this->pending->withQueryParameters($params);
        return $this;
    }

    public function attach(string $name, mixed $content, ?string $filename = null): self
    {
        return $this;
    }
}
