<?php
declare(strict_types=1);

namespace Tavaan\Engine\Security\RateLimit;

/**
 * RateLimiter — محدودکنندهٔ نرخ درخواست.
 *
 * جلوگیری از brute force و滥用.
 *
 * الگو: Fixed Window Counter
 */
final class RateLimiter
{
    /** @var array<string, array{count: int, reset_at: int}> */
    private array $buckets = [];

    public function __construct(
        private readonly int $maxAttempts = 60,
        private readonly int $decaySeconds = 60,
    ) {}

    /**
     * ثبت یک تلاش برای کلید مشخص.
     *
     * @param string $key کلید (مثلاً IP + route)
     * @return bool آیا اجازهٔ تلاش دارد؟
     */
    public function hit(string $key): bool
    {
        $now = time();

        if (!isset($this->buckets[$key]) || $this->buckets[$key]['reset_at'] < $now) {
            $this->buckets[$key] = ['count' => 1, 'reset_at' => $now + $this->decaySeconds];
            return true;
        }

        if ($this->buckets[$key]['count'] >= $this->maxAttempts) {
            return false;
        }

        $this->buckets[$key]['count']++;
        return true;
    }

    /**
     * تعداد تلاش‌های باقی‌مانده.
     */
    public function remaining(string $key): int
    {
        if (!isset($this->buckets[$key]) || $this->buckets[$key]['reset_at'] < time()) {
            return $this->maxAttempts;
        }
        return max(0, $this->maxAttempts - $this->buckets[$key]['count']);
    }

    /**
     * ثانیه‌های باقی‌مانده تا reset.
     */
    public function availableIn(string $key): int
    {
        if (!isset($this->buckets[$key])) {
            return 0;
        }
        return max(0, $this->buckets[$key]['reset_at'] - time());
    }

    /**
     * پاک کردن تلاش‌های یک کلید.
     */
    public function clear(string $key): void
    {
        unset($this->buckets[$key]);
    }

    /**
     * بررسی اینکه آیا کلید مسدود شده است.
     */
    public function tooManyAttempts(string $key): bool
    {
        if (!isset($this->buckets[$key]) || $this->buckets[$key]['reset_at'] < time()) {
            return false;
        }
        return $this->buckets[$key]['count'] >= $this->maxAttempts;
    }
}
