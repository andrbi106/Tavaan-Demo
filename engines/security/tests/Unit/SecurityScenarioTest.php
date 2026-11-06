<?php

declare(strict_types=1);

namespace Tavaan\Engine\Security\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Security\Csrf;
use Tavaan\Engine\Security\RateLimit\RateLimiter;

/**
 * T-NN scenario tests for the security engine (real implementations).
 */
final class SecurityScenarioTest extends TestCase
{
    public function test_t01_csrf_token_verify_roundtrip(): void
    {
        // Scenario T-01
$csrf = new Csrf();
        $tok = $csrf->token();
        $this->assertTrue($csrf->verify($tok));
    }

    public function test_t03_rate_limit_hit_counter(): void
    {
        // Scenario T-03
$rl = new RateLimiter(maxAttempts: 5);
        $count = $rl->hit("test");
        $this->assertSame(1, $count);
        $count = $rl->hit("test");
        $this->assertSame(2, $count);
    }
}
