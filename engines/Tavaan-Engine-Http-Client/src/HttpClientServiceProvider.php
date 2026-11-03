<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient;

/**
 * HttpClientServiceProvider — ثبت موتور HttpClient.
 */
final class HttpClientServiceProvider
{
    private ?HttpClient $client = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->client = new HttpClient($this->config);
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [HttpClient::class, PendingRequest::class, Response::class];
    }

    public function getClient(): HttpClient
    {
        if ($this->client === null) $this->register();
        return $this->client;
    }
}
