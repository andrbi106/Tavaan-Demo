<?php

declare(strict_types=1);

namespace Tavaan\Engine\Queue;

/**
 * Worker — pulls jobs from a queue and executes them.
 */
final class Worker
{
    private bool $running = false;
    private int $processed = 0;
    private int $failed = 0;
    /** @var callable|null */
    private $handler;

    public function __construct(
        private readonly QueueManager $manager,
        ?callable $handler = null,
    ) {
        $this->handler = $handler;
    }

    /**
     * run — process up to $max jobs and exit.
     * @return array{processed: int, failed: int}
     */
    public function run(int $max = 100, string $queue = 'default'): array
    {
        $this->running = true;
        for ($i = 0; $i < $max && $this->running; $i++) {
            $job = $this->manager->pop($queue);
            if ($job === null) { break; }
            try {
                if ($this->handler !== null) {
                    ($this->handler)($job);
                } elseif (is_callable($job->payload)) {
                    ($job->payload)();
                }
                $this->manager->delete($job);
                $this->processed++;
            } catch (\Throwable $e) {
                $this->manager->release($job, delay: 60);
                $this->failed++;
            }
        }
        return ['processed' => $this->processed, 'failed' => $this->failed];
    }

    /**
     * stop — signal the worker loop to halt after current job.
     */
    public function stop(): void
    {
        $this->running = false;
    }

    /**
     * daemon — run forever (until stop()), polling every $sleepMs.
     */
    public function daemon(string $queue = 'default', int $sleepMs = 1000): void
    {
        $this->running = true;
        while ($this->running) {
            $job = $this->manager->pop($queue);
            if ($job === null) {
                usleep($sleepMs * 1000);
                continue;
            }
            try {
                if ($this->handler !== null) {
                    ($this->handler)($job);
                } elseif (is_callable($job->payload)) {
                    ($job->payload)();
                }
                $this->manager->delete($job);
                $this->processed++;
            } catch (\Throwable $e) {
                $this->manager->release($job, delay: 60);
                $this->failed++;
            }
        }
    }

    public function getStats(): array
    {
        return ['processed' => $this->processed, 'failed' => $this->failed];
    }
}
