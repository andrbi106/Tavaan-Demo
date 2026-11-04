<?php

declare(strict_types=1);

namespace Tavaan\Engine\Payment\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Payment\PaymentManager;
use Tavaan\Engine\Payment\PaymentResult;

/**
 * T-NN scenario tests for the payment engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class PaymentScenarioTest extends TestCase
{
    public function test_t01_request_returns_payment_result(): void
    {
        // Scenario T-01
$m = new PaymentManager(["default" => "mock"]);
        $r = $m->request(1000, ["description" => "test"]);
        $this->assertInstanceOf(PaymentResult::class, $r);
        $this->assertTrue($r->success);
    }
    public function test_t02_verify_mock_authority(): void
    {
        // Scenario T-02
$m = new PaymentManager();
        $r = $m->verify("mock_test", 1000);
        $this->assertTrue($r->success);
    }
    public function test_t03_verify_invalid_authority_fails(): void
    {
        // Scenario T-03
$m = new PaymentManager();
        $r = $m->verify("invalid", 1000);
        $this->assertFalse($r->success);
    }
}
