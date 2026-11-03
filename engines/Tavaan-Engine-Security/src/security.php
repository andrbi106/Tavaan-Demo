<?php
declare(strict_types=1);
namespace Tavaan\Engine\Http\Security;

final class SecurityHeadersMiddleware
{
    public function __construct(private readonly array $config = []) {}
    public function handle(array $request, callable $next): array
    {
        $response = $next($request);
        $headers = $response['headers'] ?? [];
        if ($this->config['x_frame_options'] ?? true) $headers['X-Frame-Options'] = 'SAMEORIGIN';
        if ($this->config['x_content_type_options'] ?? true) $headers['X-Content-Type-Options'] = 'nosniff';
        if ($this->config['x_xss_protection'] ?? false) $headers['X-XSS-Protection'] = '1; mode=block';
        if ($this->config['hsts_enabled'] ?? false) $headers['Strict-Transport-Security'] = 'max-age=' . ($this->config['hsts_max_age'] ?? 31536000);
        if (!empty($this->config['csp'] ?? '')) $headers['Content-Security-Policy'] = $this->config['csp'];
        if (!empty($this->config['referrer_policy'] ?? '')) $headers['Referrer-Policy'] = $this->config['referrer_policy'];
        $response['headers'] = $headers;
        return $response;
    }
}

final class CsrfToken
{
    private string $token;

    public function __construct(private readonly array $config = [])
    {
        $this->token = bin2hex(random_bytes(16));
    }

    public function getToken(): string { return $this->token; }
    public function setToken(string $token): void { $this->token = $token; }

    public function verify(string $token): bool
    {
        return hash_equals($this->token, $token);
    }

    public function hiddenField(): string
    {
        return '<input type="hidden" name="_token" value="' . $this->token . '">';
    }
}

final class SsrfGuard
{
    private array $blocked = ['127.0.0.1', '0.0.0.0', '169.254.169.254', 'localhost'];
    private array $blockedRanges = ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16'];

    public function __construct(private readonly array $config = []) {}

    public function isAllowed(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if ($host === null || $host === false) return false;
        $host = strtolower($host);
        if (in_array($host, $this->blocked, true)) return false;
        $ip = gethostbyname($host);
        if ($ip === $host) return false;  // DNS failed
        foreach ($this->blockedRanges as $range) {
            if ($this->ipInRange($ip, $range)) return false;
        }
        return true;
    }

    public function assertAllowed(string $url): void
    {
        if (!$this->isAllowed($url)) throw new \RuntimeException("SSRF: URL '{$url}' is blocked.");
    }

    private function ipInRange(string $ip, string $range): bool
    {
        [$subnet, $mask] = explode('/', $range);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}

final class SecurityServiceProvider
{
    private ?CsrfToken $csrf = null;
    private ?SsrfGuard $ssrf = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->csrf = new CsrfToken($this->config); $this->ssrf = new SsrfGuard($this->config); }
    public function boot(): void {}
    public function provides(): array { return [CsrfToken::class, SsrfGuard::class, SecurityHeadersMiddleware::class]; }
    public function getCsrf(): CsrfToken { if ($this->csrf === null) $this->register(); return $this->csrf; }
    public function getSsrfGuard(): SsrfGuard { if ($this->ssrf === null) $this->register(); return $this->ssrf; }
}
