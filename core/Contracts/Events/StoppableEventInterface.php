<?php
declare(strict_types=1);
namespace Tavaan\Core\Contracts\Events;

/**
 * رویدادی که قابل توقف است ( propagation را متوقف می‌کند).
 * سازگار با PSR-14 StoppableEventInterface.
 */
interface StoppableEventInterface
{
    public function isPropagationStopped(): bool;
}
