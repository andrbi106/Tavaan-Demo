<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Events;

/**
 * رویدادی که قابل توقف است ( propagation را متوقف می‌کند).
 * الگوی استاندارد event propagation در فریمورک تَوان.
 */
interface StoppableEventInterface
{
    public function isPropagationStopped(): bool;
}
