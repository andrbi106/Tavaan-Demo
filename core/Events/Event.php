<?php
declare(strict_types=1);
namespace Tavaan\Core\Events;

use Tavaan\Core\Contracts\Events\StoppableEventInterface;

/**
 * کلاس پایهٔ همهٔ رویدادها.
 * رویدادها می‌توانند ساده باشند (فقط نام) یا شیء با داده.
 */
class Event implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function stopPropagation(): static
    {
        $this->propagationStopped = true;
        return $this;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
