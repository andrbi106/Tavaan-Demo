<?php

declare(strict_types=1);

namespace Tavaan\Engine\Tenancy;

/**
 * TenantContext — context جاری (Fiber-aware در PHP 8.2+).
 *
 * نگه‌داری tenant جاری در طول یک درخواست/Job.
 */
final class TenantContext
{
    private static ?Tenant $current = null;

    public static function current(): ?Tenant
    {
        return self::$current;
    }

    public static function setCurrent(?Tenant $tenant): void
    {
        self::$current = $tenant;
    }

    public static function clear(): void
    {
        self::$current = null;
    }

    public static function isSet(): bool
    {
        return self::$current !== null;
    }

    /**
     * اجرای یک callback در زمینۀ یک tenant دیگر و بازگشت به tenant قبلی.
     */
    public static function run(Tenant $tenant, \Closure $callback): mixed
    {
        $previous = self::$current;
        self::$current = $tenant;
        try {
            return $callback();
        } finally {
            self::$current = $previous;
        }
    }

    /**
     * اجرای callback بدون tenant جاری (cross-tenant, با مجوز).
     */
    public static function withoutCurrent(\Closure $callback): mixed
    {
        $previous = self::$current;
        self::$current = null;
        try {
            return $callback();
        } finally {
            self::$current = $previous;
        }
    }
}
