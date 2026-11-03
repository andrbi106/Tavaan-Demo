<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event;

use Tavaan\Engine\Event\Contracts\DispatcherInterface;

/**
 * EventServiceProvider — ثبت موتور Event.
 */
final class EventServiceProvider
{
    private ?EventDispatcher $dispatcher = null;

    public function __construct(
        private readonly ?object $container = null,
    ) {
    }

    public function register(): void
    {
        $this->dispatcher = new EventDispatcher($this->container);
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
        return [DispatcherInterface::class, EventDispatcher::class];
    }

    public function getDispatcher(): EventDispatcher
    {
        if ($this->dispatcher === null) {
            $this->register();
        }
        return $this->dispatcher;
    }
}
