<?php

declare(strict_types=1);

namespace Tavaan\Engine\Realtime;

/**
 * Broadcast — facade-style helpers for broadcasting events.
 */
final class Broadcast
{
    public function __construct(private readonly RealtimeManager $manager = new RealtimeManager()) {}

    /**
     * channel — get a named channel by name.
     */
    public function channel(string $name): Channel
    {
        return $this->manager->channel($name);
    }

    /**
     * connection — return the underlying RealtimeManager (which holds all channels).
     */
    public function connection(string $name = 'default'): RealtimeManager
    {
        return $this->manager;
    }

    public function to(string $channel): self
    {
        $this->channel($channel);
        return $this;
    }

    public function event(string $event, mixed $data): void
    {
        $this->manager->broadcast($this->lastChannel ?? 'public', $event, $data);
    }

    private ?string $lastChannel = null;
}
