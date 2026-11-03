<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event;

/**
 * OutboxRelay — پیاده‌سازی مطابق مستندات.
 *
 * @see docs/03-engines/ (YAML machine-readable block)
 */
final class OutboxRelay
{
    /**
     * relay
     */
    public function relay(int $batch = 100): int
    {
        $count = 0;
        $pending = $this->repository->getPending($batch);
        foreach ($pending as $event) {
            $this->dispatch($event);
            $this->repository->markProcessed($event["id"]);
            $count++;
        }
        return $count;
    }
    /**
     * retryDead
     */
    public function retryDead(int $batch = 10): int
    {
        $count = 0;
        $dead = $this->repository->getDead($batch);
        foreach ($dead as $event) {
            $this->dispatch($event);
            $this->repository->markProcessed($event["id"]);
            $count++;
        }
        return $count;
    }
    /**
     * purge
     */
    public function purge(int $olderThanDays = 30): int
    {
        return $this->repository->purgeOlder($olderThanDays);
    }
}
