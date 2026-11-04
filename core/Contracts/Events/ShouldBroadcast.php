<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Events;

/**
 * رویدادی که باید broadcast شود (realtime به کلاینت‌ها).
 */
interface ShouldBroadcast
{
    /** @return list<string>  نام کانال‌ها */
    public function broadcastOn(): array;
}
