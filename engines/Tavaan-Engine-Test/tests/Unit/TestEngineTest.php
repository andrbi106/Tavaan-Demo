<?php

declare(strict_types=1);

namespace Tavaan\Testing\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Testing\TestRunner;
use Tavaan\Testing\TestResult;
use Tavaan\Testing\TestSuiteResult;
use Tavaan\Testing\Mock\Mock;
use Tavaan\Testing\Exceptions\AssertionFailedException;
use Tavaan\Testing\Exceptions\TestIncompleteException;

/**
 * تست‌های Unit برای موتور Test.
 *
 * تست موتور Test با خود TestCase.
 */
final class TestEngineTest extends TestCase
{
    public function test_assert_true_passes(): void
    {
        $this->assertTrue(true);
    }

    public function test_assert_true_fails_on_false(): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->assertTrue(false);
    }

    public function test_assert_false_passes(): void
    {
        $this->assertFalse(false);
    }

    public function test_assert_same_with_strings(): void
    {
        $this->assertSame('hello', 'hello');
    }

    public function test_assert_same_with_integers(): void
    {
        $this->assertSame(42, 42);
    }

    public function test_assert_same_with_objects(): void
    {
        $obj = new \stdClass();
        $this->assertSame($obj, $obj);
    }

    public function test_assert_not_same(): void
    {
        $this->assertNotSame(new \stdClass(), new \stdClass());
    }

    public function test_assert_null(): void
    {
        $this->assertNull(null);
    }

    public function test_assert_not_null(): void
    {
        $this->assertNotNull('value');
    }

    public function test_assert_count(): void
    {
        $this->assertCount(3, [1, 2, 3]);
    }

    public function test_assert_count_fails(): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->assertCount(2, [1, 2, 3]);
    }

    public function test_assert_contains_in_array(): void
    {
        $this->assertContains(2, [1, 2, 3]);
    }

    public function test_assert_contains_in_string(): void
    {
        $this->assertContains('wor', 'hello world');
    }

    public function test_assert_array_has_key(): void
    {
        $this->assertArrayHasKey('name', ['name' => 'ali', 'age' => 30]);
    }

    public function test_assert_instance_of(): void
    {
        $this->assertInstanceOf(\stdClass::class, new \stdClass());
    }

    public function test_assert_string_contains_string(): void
    {
        $this->assertStringContainsString('hello', 'hello world');
    }

    public function test_assert_matches_regex(): void
    {
        $this->assertMatchesRegex('/^\d+$/', '12345');
    }

    public function test_assert_greater_than(): void
    {
        $this->assertGreaterThan(5, 10);
    }

    public function test_assert_less_than(): void
    {
        $this->assertLessThan(10, 5);
    }

    public function test_assert_equals_with_delta(): void
    {
        $this->assertEqualsWithDelta(3.14, 3.14159, 0.01);
    }

    public function test_assert_empty(): void
    {
        $this->assertEmpty([]);
        $this->assertEmpty('');
        $this->assertEmpty(null);
    }

    public function test_assert_not_empty(): void
    {
        $this->assertNotEmpty([1]);
        $this->assertNotEmpty('text');
    }

    public function test_mark_test_incomplete(): void
    {
        $this->expectException(TestIncompleteException::class);
        $this->markTestIncomplete('not yet implemented');
    }

    public function test_expect_exception_caught(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        throw new \InvalidArgumentException('test');
    }

    public function test_expect_exception_message_match(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('test');
        throw new \InvalidArgumentException('test message');
    }

    public function test_expect_exception_code_match(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(42);
        throw new \InvalidArgumentException('test', 42);
    }

    public function test_fail_explicit(): void
    {
        $this->expectException(AssertionFailedException::class);
        $this->fail('explicit failure');
    }

    // === TestResult / TestRunner ===

    public function test_test_result_pass(): void
    {
        $r = new TestResult('testX', self::class);
        $r->pass();
        $this->assertTrue($r->isPassed());
    }

    public function test_test_result_fail(): void
    {
        $r = new TestResult('testX', self::class);
        $r->fail('something failed');
        $this->assertFalse($r->isPassed());
        $this->assertSame('something failed', $r->failureMessage);
    }

    public function test_test_runner_runs_methods(): void
    {
        $runner = new TestRunner([new SimpleTestCase()]);
        $result = $runner->run();
        $this->assertSame(2, $result->total);
        $this->assertSame(2, $result->passed);
    }

    public function test_test_runner_handles_failure(): void
    {
        $runner = new TestRunner([new FailingTestCase()]);
        $result = $runner->run();
        $this->assertSame(1, $result->total);
        $this->assertSame(0, $result->passed);
        $this->assertSame(1, $result->failed);
        $this->assertFalse($result->isPassed());
    }

    public function test_test_suite_result_summary(): void
    {
        $r = new TestSuiteResult(total: 5, passed: 4, failed: 1, results: []);
        $this->assertSame('FAIL: 4/5 (failed: 1)', $r->summary());
        $r2 = new TestSuiteResult(total: 5, passed: 5, failed: 0, results: []);
        $this->assertSame('PASS: 5/5 (failed: 0)', $r2->summary());
        $this->assertTrue($r2->isPassed());
    }

    // === Mock ===

    public function test_mock_returns_set_value(): void
    {
        $mock = Mock::of('TestInterface');
        $mock->shouldReceive('getValue')->andReturn(42);
        $m = $mock->getMock();
        $this->assertSame(42, $m->getValue());
    }

    public function test_mock_returns_with_args(): void
    {
        $mock = Mock::of('TestInterface');
        $mock->shouldReceive('get')->with('key')->andReturn('value');
        $m = $mock->getMock();
        $this->assertSame('value', $m->get('key'));
    }

    public function test_mock_returns_using_callback(): void
    {
        $mock = Mock::of('TestInterface');
        $mock->shouldReceive('compute')->andReturnUsing(fn(int $x) => $x * 2);
        $m = $mock->getMock();
        $this->assertSame(10, $m->compute(5));
    }

    public function test_mock_throws_when_no_expectation(): void
    {
        $mock = Mock::of('TestInterface');
        $m = $mock->getMock();
        $this->expectException(\RuntimeException::class);
        $m->unexpectedMethod();
    }
}

/**
 * Helper TestCase برای تست Runner.
 */
final class SimpleTestCase extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }

    public function testTwo(): void
    {
        $this->assertSame(1, 1);
    }
}

final class FailingTestCase extends TestCase
{
    public function testFail(): void
    {
        $this->fail('expected fail');
    }
}
