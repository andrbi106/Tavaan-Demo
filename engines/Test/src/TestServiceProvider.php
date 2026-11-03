<?php

declare(strict_types=1);

namespace Tavaan\Testing;

/**
 * TestServiceProvider — ثبت موتور Test.
 *
 * توجه: موتور Test خودش ابزار تست است؛ بنابراین این Provider فقط یک accessor ثبت می‌کند.
 */
final class TestServiceProvider
{
    public function register(): void
    {
        // No-op — the test engine is invoked directly via TestRunner
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
        return [TestCase::class, TestRunner::class];
    }
}
