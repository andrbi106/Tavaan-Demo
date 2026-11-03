<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient;

/**
 * Pool — درخواست‌های موازی.
 */
final class Pool
{
    /** @var array<string, array{method: string, url: string, data: mixed}> */
    private array $requests = [];

    public function as(string $name): self
    {
        // Marker for next request
        $this->currentKey = $name;
        return $this;
    }

    private ?string $currentKey = null;

    public function get(string $url): self
    {
        $key = $this->currentKey ?? \uniqid('req_');
        $this->requests[$key] = ['method' => 'GET', 'url' => $url, 'data' => null];
        $this->currentKey = null;
        return $this;
    }

    public function post(string $url, mixed $data = null): self
    {
        $key = $this->currentKey ?? \uniqid('req_');
        $this->requests[$key] = ['method' => 'POST', 'url' => $url, 'data' => $data];
        $this->currentKey = null;
        return $this;
    }

    /**
     * @return array<string, Response>
     */
    public function execute(): array
    {
        $results = [];
        foreach ($this->requests as $key => $req) {
            $pending = new PendingRequest();
            try {
                $results[$key] = $pending->send($req['method'], $req['url'], $req['data']);
            } catch (\Throwable $e) {
                // Skip failed
            }
        }
        return $results;
    }
}
