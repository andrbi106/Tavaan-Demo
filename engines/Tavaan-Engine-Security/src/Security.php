<?php

declare(strict_types=1);

namespace Tavaan\Engine\Security;

final class Security
{
    private string $signingKey;

    public function __construct(string $signingKey = '')
    {
        $this->signingKey = $signingKey;
    }

    public function sanitize(string $input): string
    {
        $input = strip_tags($input);
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $input = trim($input);
        return $input;
    }

    public function sign(string $data): string
    {
        return hash_hmac('sha256', $data, $this->signingKey);
    }

    public function validateSignature(string $data, string $signature): bool
    {
        $expected = $this->sign($data);
        return hash_equals($expected, $signature);
    }

    public function normalize(string $input): string
    {
        $input = normalizer_normalize($input) ?? $input;
        $input = preg_replace('/[\x00-\x1F\x7F]/u', '', $input);
        return $input;
    }

    public function ssrfCheck(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if ($host === null || $host === false) return false;
        $host = strtolower($host);
        $blocked = ['127.0.0.1', '0.0.0.0', '169.254.169.254', 'localhost', '::1'];
        if (in_array($host, $blocked, true)) return false;
        $ip = gethostbyname($host);
        if ($ip === $host) return false;
        $blockedRanges = ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16'];
        foreach ($blockedRanges as $range) {
            [$subnet, $mask] = explode('/', $range);
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            $maskLong = -1 << (32 - (int)$mask);
            if (($ipLong & $maskLong) === ($subnetLong & $maskLong)) return false;
        }
        return true;
    }
}
