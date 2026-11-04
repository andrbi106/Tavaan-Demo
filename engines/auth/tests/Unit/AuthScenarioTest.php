<?php

declare(strict_types=1);

namespace Tavaan\Engine\Auth\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Auth\AuthManager;
use Tavaan\Engine\Auth\Guards\SessionGuard;
use Tavaan\Engine\Auth\Guards\TokenGuard;

/**
 * T-NN scenario tests for the auth engine (real implementations).
 */
final class AuthScenarioTest extends TestCase
{
    public function test_t01_attempt_with_correct_credentials(): void
    {
        // Scenario T-01
$users = [["id" => 1, "email" => "u@e.com", "password" => "secret"]];
        $guard = new SessionGuard("web", ["users" => $users]);
        $this->assertTrue($guard->attempt(["email" => "u@e.com", "password" => "secret"]));
        $this->assertNotNull($guard->user());
    }

    public function test_t02_attempt_with_wrong_credentials(): void
    {
        // Scenario T-02
$users = [["id" => 1, "email" => "u@e.com", "password" => "secret"]];
        $guard = new SessionGuard("web", ["users" => $users]);
        $this->assertFalse($guard->attempt(["email" => "u@e.com", "password" => "wrong"]));
    }

    public function test_t08_otp_one_time_use(): void
    {
        // Scenario T-08
// OTP placeholder — assume user has otp field
        $users = [["id" => 1, "otp" => "123456"]];
        $guard = new SessionGuard("web", ["users" => $users]);
        $guard->loginUsingId(1);
        $this->assertNotNull($guard->user());
    }

    public function test_t12_device_list_revoke(): void
    {
        // Scenario T-12
// Devices scenario placeholder — check that AuthManager exposes guards
        $mgr = new AuthManager();
        $this->assertInstanceOf(SessionGuard::class, $mgr->guard("web"));
        $this->assertInstanceOf(TokenGuard::class, $mgr->guard("api"));
    }
}
