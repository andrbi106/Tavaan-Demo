<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Log\LogManager;

/**
 * T-NN scenario tests for the log engine (real implementations).
 */
final class LogScenarioTest extends TestCase
{
    public function test_t01_log_manager_default_channel(): void
    {
        // Scenario T-01
$m = new LogManager();
        $this->assertNotNull($m->driver());
    }

    public function test_t02_named_channel_returns_logger(): void
    {
        // Scenario T-02
$m = new LogManager();
        $logger = $m->channel("custom");
        $this->assertNotNull($logger);
        $logger->info("test message");
    }
}
