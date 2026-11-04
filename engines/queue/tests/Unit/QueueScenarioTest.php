<?php

declare(strict_types=1);

namespace Tavaan\Engine\Queue\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Queue\QueueManager;
use Tavaan\Engine\Queue\Worker;

/**
 * T-NN scenario tests for the queue engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class QueueScenarioTest extends TestCase
{
    public function test_t01_push_pop(): void
    {
        // Scenario T-01
$qm = new QueueManager();
        $id = $qm->push("default", fn() => "executed");
        $this->assertSame(1, $qm->size("default"));
        $job = $qm->pop("default");
        $this->assertNotNull($job);
        $this->assertSame($id, $job->id);
    }
    public function test_t02_pop_empty_returns_null(): void
    {
        // Scenario T-02
$qm = new QueueManager();
        $this->assertNull($qm->pop("default"));
    }
    public function test_t03_delete_removes_job(): void
    {
        // Scenario T-03
$qm = new QueueManager();
        $id = $qm->push("default", "payload");
        $job = $qm->pop("default");
        $qm->delete($job);
        $this->assertSame(0, $qm->size("default"));
    }
    public function test_t04_release_returns_job_to_queue(): void
    {
        // Scenario T-04
$qm = new QueueManager();
        $qm->push("default", "p");
        $job = $qm->pop("default");
        $qm->release($job, delay: 0);
        $this->assertSame(1, $qm->size("default"));
    }
    public function test_t05_worker_runs_handler(): void
    {
        // Scenario T-05
$qm = new QueueManager();
        $hit = 0;
        $hitRef = &$hit;
        $qm->push("default", function () use (&$hitRef) { $hitRef++; });
        $w = new Worker($qm);
        $stats = $w->run(max: 10);
        $this->assertSame(1, $stats["processed"]);
        $this->assertSame(1, $hit);
    }
    public function test_t06_get_queues(): void
    {
        // Scenario T-06
$qm = new QueueManager(["default" => "default", "queues" => ["q1" => [], "q2" => []]]);
        $queues = $qm->getQueues();
        $this->assertContains("default", $queues);
        $this->assertContains("q1", $queues);
        $this->assertContains("q2", $queues);
    }
}
