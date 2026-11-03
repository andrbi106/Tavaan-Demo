<?php

declare(strict_types=1);

namespace Tavaan\Engine\Auth\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Auth\AuthManager;
use Tavaan\Engine\Auth\SessionGuard;
use Tavaan\Engine\Auth\TokenGuard;

/**
 * Feature tests for the auth engine.
 */
final class AuthFeatureTest extends TestCase
{
    public function test_attempt_with_valid_credentials_succeeds(): void
    {
$users = [["id" => 1, "email" => "ali@ex.com", "password" => "secret"]];
        $guard = new SessionGuard("web", ["users" => $users]);
        $ok = $guard->attempt(["email" => "ali@ex.com", "password" => "secret"]);
        $this->assertTrue($ok);
        $this->assertNotNull($guard->user());
        $this->assertSame(1, $guard->id());
    }

    public function test_attempt_with_wrong_email_fails(): void
    {
$guard = new SessionGuard("web", ["users" => []]);
        $ok = $guard->attempt(["email" => "missing@x.com", "password" => "p"]);
        $this->assertFalse($ok);
        $this->assertNull($guard->user());
    }

    public function test_token_guard_with_valid_token(): void
    {
$tokens = [
            ["id" => 1, "email" => "u@x.com", "api_token" => "tok_abc"],
        ];
        $guard = new TokenGuard("api", ["tokens" => $tokens]);
        $ok = $guard->attempt(["api_token" => "tok_abc"]);
        $this->assertTrue($ok);
        $this->assertSame(1, $guard->id());
    }

    public function test_token_guard_with_invalid_token(): void
    {
$guard = new TokenGuard("api", ["tokens" => []]);
        $ok = $guard->attempt(["api_token" => "unknown"]);
        $this->assertFalse($ok);
    }

    public function test_login_using_id_finds_user(): void
    {
$users = [["id" => 7, "email" => "u@x.com"]];
        $guard = new SessionGuard("web", ["users" => $users]);
        $this->assertTrue($guard->loginUsingId(7));
        $this->assertSame(7, $guard->id());
    }

    public function test_logout_clears_user(): void
    {
$users = [["id" => 1, "email" => "u@x.com", "password" => "p"]];
        $guard = new SessionGuard("web", ["users" => $users]);
        $guard->attempt(["email" => "u@x.com", "password" => "p"]);
        $this->assertTrue($guard->check());
        $guard->logout();
        $this->assertFalse($guard->check());
    }

    public function test_guest_returns_true_when_not_logged_in(): void
    {
$mgr = new AuthManager();
        $this->assertTrue($mgr->guest());
        $this->assertFalse($mgr->check());
    }

    public function test_default_guard_is_web(): void
    {
$mgr = new AuthManager();
        $guard = $mgr->guard();
        $this->assertInstanceOf(SessionGuard::class, $guard);
    }
}
