<?php

declare(strict_types=1);

namespace Tavaan\Engine\Schedule\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Schedule\CronExpression;
use Tavaan\Engine\Schedule\ScheduleManager;
use Tavaan\Engine\Schedule\ScheduledTask;

/**
 * T-NN scenario tests for the schedule engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class ScheduleScenarioTest extends TestCase
{
    public function test_t01_cron_every_minute_matches(): void
    {
        // Scenario T-01
$c = new CronExpression("* * * * *");
        $this->assertTrue($c->isDue(new \DateTime("2026-11-04 12:00:00")));
    }
    public function test_t02_cron_5_minute_step(): void
    {
        // Scenario T-02
$c = new CronExpression("*/5 * * * *");
        $this->assertTrue($c->isDue(new \DateTime("2026-11-04 12:00:00")));
        $this->assertTrue($c->isDue(new \DateTime("2026-11-04 12:05:00")));
        $this->assertFalse($c->isDue(new \DateTime("2026-11-04 12:03:00")));
    }
    public function test_t03_cron_hourly(): void
    {
        // Scenario T-03
$c = new CronExpression("0 * * * *");
        $this->assertTrue($c->isDue(new \DateTime("2026-11-04 12:00:00")));
        $this->assertFalse($c->isDue(new \DateTime("2026-11-04 12:30:00")));
    }
    public function test_t04_next_run_dates_returns_count(): void
    {
        // Scenario T-04
$c = new CronExpression("*/5 * * * *");
        $dates = $c->nextRunDates(3);
        $this->assertCount(3, $dates);
    }
    public function test_t05_schedule_manager_add_run(): void
    {
        // Scenario T-05
$mgr = new ScheduleManager();
        $hit = 0;
        $hitRef = &$hit;
        $mgr->add(new ScheduledTask("t1", function () use (&$hitRef) { $hitRef++; }, "* * * * *"));
        $now = new \DateTime();
        $mgr->runDue($now);
        $this->assertSame(1, $hit);
    }
}
