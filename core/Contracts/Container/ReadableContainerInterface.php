<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Readable Container Interface
 *
 * قرارداد فقط‌خواندنیِ کانتینر: دو عملیات بنیادیِ lookup (get و has).
 * این رابط توسط ContainerInterface کامل extend می‌شود.
 *
 * تَوان قانون ZeroDependency را رعایت می‌کند — کاملاً خودکفا، بدون وابستگی بیرونی.
 */

namespace Tavaan\Core\Contracts\Container;

interface ReadableContainerInterface
{
    /**
     * یافتن و بازگرداندن یک ورودی از کانتینر با شناسه.
     *
     * @param string $id شناسهٔ ورودی.
     *
     * @throws \Throwable اگر ورودی یافت نشود یا خطا در بازیابی رخ دهد.
     *
     * @return mixed ورودی.
     */
    public function get(string $id): mixed;

    /**
     * بررسی اینکه آیا کانتینر می‌تواند ورودی با شناسهٔ داده‌شده را بازگرداند.
     *
     * @param string $id شناسهٔ ورودی.
     *
     * @return bool
     */
    public function has(string $id): bool;
}
