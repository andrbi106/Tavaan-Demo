<?php

declare(strict_types=1);

namespace Tavaan\Engine\Security\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Security\Csrf;
use Tavaan\Engine\Security\RateLimiter;

/**
 * Feature tests for the security engine.
 */
final class SecurityFeatureTest extends TestCase
{
    public function test_csrf_token_returns_string(): void
    {
$csrf = new Csrf();
        $tok = $csrf->token();
        $this->assertNotEmpty($tok);
    }

    public function test_csrf_verify_correct_token(): void
    {
$csrf = new Csrf();
        $tok = $csrf->token();
        $this->assertTrue($csrf->verify($tok));
    }

    public function test_csrf_verify_wrong_returns_false(): void
    {
$csrf = new Csrf();
        $csrf->token();
        $this->assertFalse($csrf->verify("wrong-token"));
    }

    public function test_csrf_rotate_changes_token(): void
    {
$csrf = new Csrf();
        $tok1 = $csrf->token();
        $csrf->rotate();
        $tok2 = $csrf->token();
        $this->assertNotSame($tok1, $tok2);
    }

    public function test_rate_limiter_allows_under_limit(): void
    {
$rl = new RateLimiter(maxAttempts: 5);
        for ($i = 0; $i < 5; $i++) {
            $n = $rl->hit("test");
            $this->assertGreaterThan(0, $n);
        }
        $this->assertTrue($rl->tooManyAttempts("test"));
    }

    public function test_rate_limiter_blocks_over_limit(): void
    {
$rl = new RateLimiter(maxAttempts: 3);
        for ($i = 0; $i < 3; $i++) { $rl->hit("k"); }
        $this->assertTrue($rl->tooManyAttempts("k"));
    }

    public function test_rate_limiter_clear_resets(): void
    {
$rl = new RateLimiter(maxAttempts: 2);
        $rl->hit("k");
        $rl->hit("k");
        $this->assertTrue($rl->tooManyAttempts("k"));
        $rl->clear("k");
        $this->assertFalse($rl->tooManyAttempts("k"));
    }

    public function test_rate_limiter_attempt_returns_callback_result(): void
    {
$rl = new RateLimiter(maxAttempts: 10);
        $result = $rl->attempt("test", fn() => "ok");
        $this->assertSame("ok", $result);
    }
}
