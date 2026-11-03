<?php

declare(strict_types=1);

namespace Tavaan\Engine\HttpClient;

/**
 * PendingRequest — ساخت fluent یک درخواست HTTP.
 *
 * @see docs/03-engines/http-client.md (DOC-064)
 */
final class PendingRequest
{
    /** @var array<string, string> */
    private array $headers = [];
    /** @var array<string, string> */
    private array $query = [];
    private ?string $body = null;
    private ?string $contentType = null;
    private int $timeout = 30;
    private int $tries = 1;
    private int $retryDelay = 100;
    /** @var array<string, string> */
    private array $cookies = [];
    private bool $verifyTls = true;
    private ?string $bearerToken = null;

    public function withHeaders(array $headers): self
    {
        foreach ($headers as $k => $v) $this->headers[$k] = (string) $v;
        return $this;
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withToken(string $token, string $type = 'Bearer'): self
    {
        $this->bearerToken = "{$type} {$token}";
        $this->headers['Authorization'] = $this->bearerToken;
        return $this;
    }

    public function withQueryParameters(array $query): self
    {
        foreach ($query as $k => $v) $this->query[$k] = (string) $v;
        return $this;
    }

    public function asJson(): self
    {
        $this->contentType = 'application/json';
        $this->headers['Content-Type'] = $this->contentType;
        $this->headers['Accept'] = 'application/json';
        return $this;
    }

    public function asForm(): self
    {
        $this->contentType = 'application/x-www-form-urlencoded';
        $this->headers['Content-Type'] = $this->contentType;
        return $this;
    }

    public function acceptJson(): self
    {
        $this->headers['Accept'] = 'application/json';
        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function retry(int $times, int $delayMs = 100): self
    {
        $this->tries = $times;
        $this->retryDelay = $delayMs;
        return $this;
    }

    public function withCookies(array $cookies, ?string $domain = null): self
    {
        foreach ($cookies as $k => $v) $this->cookies[$k] = (string) $v;
        return $this;
    }

    public function withoutTlsVerification(): self
    {
        $this->verifyTls = false;
        return $this;
    }

    public function get(string $url): Response
    {
        return $this->send('GET', $url);
    }

    public function post(string $url, mixed $data = null): Response
    {
        return $this->send('POST', $url, $data);
    }

    public function put(string $url, mixed $data = null): Response
    {
        return $this->send('PUT', $url, $data);
    }

    public function patch(string $url, mixed $data = null): Response
    {
        return $this->send('PATCH', $url, $data);
    }

    public function delete(string $url, mixed $data = null): Response
    {
        return $this->send('DELETE', $url, $data);
    }

    public function head(string $url): Response
    {
        return $this->send('HEAD', $url);
    }

    public function send(string $method, string $url, mixed $data = null): Response
    {
        if ($data !== null) {
            if ($this->contentType === 'application/json') {
                $this->body = \json_encode($data, \JSON_UNESCAPED_UNICODE) ?: '';
            } elseif ($this->contentType === 'application/x-www-form-urlencoded') {
                $this->body = \http_build_query($data);
            } elseif (\is_string($data)) {
                $this->body = $data;
            } elseif (\is_array($data)) {
                $this->body = \http_build_query($data);
            } else {
                $this->body = (string) $data;
            }
        }
        if (!empty($this->query)) {
            $separator = \str_contains($url, '?') ? '&' : '?';
            $url .= $separator . \http_build_query($this->query);
        }
        if (!empty($this->cookies)) {
            $this->headers['Cookie'] = \http_build_query($this->cookies, '', '; ');
        }
        // Send with retries
        $lastError = null;
        for ($attempt = 1; $attempt <= $this->tries; $attempt++) {
            try {
                return $this->doRequest($method, $url);
            } catch (\Throwable $e) {
                $lastError = $e;
                if ($attempt < $this->tries) {
                    \usleep($this->retryDelay * 1000);
                }
            }
        }
        throw new Exceptions\HttpClientException(
            'Request failed after ' . $this->tries . ' attempts: ' . $lastError?->getMessage(),
            previous: $lastError
        );
    }

    private function doRequest(string $method, string $url): Response
    {
        // Try curl first
        if (\function_exists('curl_init')) {
            return $this->curlRequest($method, $url);
        }
        return $this->streamRequest($method, $url);
    }

    private function curlRequest(string $method, string $url): Response
    {
        $ch = \curl_init();
        \curl_setopt($ch, \CURLOPT_URL, $url);
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, $method);
        \curl_setopt($ch, \CURLOPT_TIMEOUT, $this->timeout);
        \curl_setopt($ch, \CURLOPT_HTTPHEADER, \array_map(
            fn($k, $v) => "{$k}: {$v}",
            \array_keys($this->headers),
            \array_values($this->headers)
        ));
        if ($this->body !== null) {
            \curl_setopt($ch, \CURLOPT_POSTFIELDS, $this->body);
        }
        \curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, $this->verifyTls);
        \curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, $this->verifyTls ? 2 : 0);
        $body = \curl_exec($ch);
        if ($body === false) {
            $err = \curl_error($ch);
            \curl_close($ch);
            throw new Exceptions\HttpClientException("cURL error: {$err}");
        }
        $status = (int) \curl_getinfo($ch, \CURLINFO_HTTP_CODE);
        $headerSize = (int) \curl_getinfo($ch, \CURLINFO_HEADER_SIZE);
        \curl_close($ch);
        return new Response($status, (string) $body, []);
    }

    private function streamRequest(string $method, string $url): Response
    {
        $opts = [
            'http' => [
                'method' => $method,
                'header' => \implode("\r
", \array_map(
                    fn($k, $v) => "{$k}: {$v}",
                    \array_keys($this->headers),
                    \array_values($this->headers)
                )),
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => $this->verifyTls,
                'verify_peer_name' => $this->verifyTls,
            ],
        ];
        if ($this->body !== null) {
            $opts['http']['content'] = $this->body;
        }
        $context = \stream_context_create($opts);
        $body = @\file_get_contents($url, false, $context);
        if ($body === false) {
            throw new Exceptions\HttpClientException("Stream request failed");
        }
        // Parse status from $http_response_header
        $status = 200;
        $headers = [];
        if (isset($http_response_header)) {
            foreach ($http_response_header as $h) {
                if (\preg_match('/^HTTP\/[\d.]+ (\d+)/', $h, $m)) {
                    $status = (int) $m[1];
                } elseif (\str_contains($h, ':')) {
                    [$k, $v] = \explode(':', $h, 2);
                    $headers[\trim($k)] = \trim($v);
                }
            }
        }
        return new Response($status, (string) $body, $headers);
    }

    public function getHeaders(): array { return $this->headers; }
    public function getQuery(): array { return $this->query; }
    public function getBody(): ?string { return $this->body; }
    public function getTimeout(): int { return $this->timeout; }
    public function getTries(): int { return $this->tries; }
}
