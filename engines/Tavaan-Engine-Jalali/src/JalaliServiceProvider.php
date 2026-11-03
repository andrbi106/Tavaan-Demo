<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Jalali;

/**
 * JalaliServiceProvider — ثبت موتور Jalali.
 */
final class JalaliServiceProvider
{
    public function register(): void
    {
        // No-op — converter and date are static
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [JalaliConverter::class, JalaliDate::class];
    }
}
