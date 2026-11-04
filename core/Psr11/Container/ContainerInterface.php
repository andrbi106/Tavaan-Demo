<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — PSR-11 Container Interface (Stub)
 *
 * پیاده‌سازی محلیِ رابط PSR-11 (Container interface) برای حفظ سازگاری با
 * کتابخانه‌های PSR-11 بدون نیاز به نصب بستهٔ `psr/container` از طریق Composer.
 *
 * این فایل تنها زمانی توسط autoloader بارگذاری می‌شود که بستهٔ اصلی
 * `psr/container` از طریق Composer نصب نباشد. در صورت نصب Composer،
 * نسخهٔ رسمی اولویت دارد (با ثبت vendor/ در ابتدای نگاشت namespace).
 *
 * متن اصلی رابط از https://github.com/php-fig/container اقتباس شده (مجوز MIT).
 */

namespace Psr\Container;

/**
 * Describes the interface of a container that exposes methods to read its entries.
 */
interface ContainerInterface
{
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id): mixed;

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool;
}
