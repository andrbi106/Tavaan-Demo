<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Console\ConsoleKernel;
use Tavaan\Engine\Console\Command;

/**
 * T-NN scenario tests for the console engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class ConsoleScenarioTest extends TestCase
{
    public function test_t01_register_command(): void
    {
        // Scenario T-01
$kernel = new ConsoleKernel();
        $cmd = new class extends Command {
            public const NAME = "test:hello";
            public const DESCRIPTION = "A test command";
            public function handle(): int { return 0; }
        };
        $kernel->register($cmd);
        $this->assertContains("test:hello", $kernel->commands());
    }
    public function test_t02_call_returns_exit_code(): void
    {
        // Scenario T-02
$kernel = new ConsoleKernel();
        $cmd = new class extends Command {
            public const NAME = "test:ok";
            public const DESCRIPTION = "OK";
            public function handle(): int { return 0; }
        };
        $kernel->register($cmd);
        $exit = $kernel->call("test:ok", []);
        $this->assertSame(0, $exit);
    }
    public function test_t03_call_unknown_returns_nonzero(): void
    {
        // Scenario T-03
$kernel = new ConsoleKernel();
        $exit = $kernel->call("unknown:cmd");
        $this->assertSame(1, $exit);
    }
}
