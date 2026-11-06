<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Events;

/**
 * قرارداد subscriber — کلاسی که چندین رویداد را subscribe می‌کند.
 */
interface EventSubscriber
{
    /**
     * @return array<string, callable|string|array>  نگاشت event => handler
     */
    public static function subscribe(): array;
}
