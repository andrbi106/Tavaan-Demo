<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

/**
 * LogMasker — پنهان‌سازی دادهٔ حساس در context.
 */
final class LogMasker
{
    /** @var list<string> */
    private array $sensitiveKeys = ['password', 'token', 'api_key', 'secret', 'credit_card', 'cvv', 'ssn', 'pin'];

    /**
     * @param list<string> $keys
     */
    public function addSensitiveKey(string ...$keys): self
    {
        foreach ($keys as $k) {
            $this->sensitiveKeys[] = $k;
        }
        return $this;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    public function mask(array $context): array
    {
        $result = [];
        foreach ($context as $key => $value) {
            if ($this->isSensitive($key)) {
                $result[$key] = '***masked***';
            } elseif (\is_array($value)) {
                $result[$key] = $this->mask($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    private function isSensitive(string $key): bool
    {
        $lower = \strtolower($key);
        foreach ($this->sensitiveKeys as $sensitive) {
            if (\str_contains($lower, $sensitive)) {
                return true;
            }
        }
        return false;
    }

    /**
     * addKeys
     */
    public function addKeys(string ...$keys): self
    {
        foreach ($keys as $k) {
            $this->sensitiveKeys[] = $k;
        }
        return $this;
    }
    /**
     * addPatterns
     */
    public function addPatterns(string ...$patterns): self
    {
        foreach ($patterns as $p) {
            $this->sensitivePatterns[] = $p;
        }
        return $this;
    }
    /**
     * maskString
     */
    public function maskString(string $value): string
    {
        foreach ($this->sensitiveKeys as $key) {
            $value = preg_replace('/(' . preg_quote($key, '/') . '[=\s]+)(\S+)/i', '$1***MASKED***', $value);
        }
        foreach ($this->sensitivePatterns as $pattern) {
            $value = preg_replace($pattern, '***MASKED***', $value) ?? $value;
        }
        return $value;
    }
}
