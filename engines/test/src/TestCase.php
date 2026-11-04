<?php

declare(strict_types=1);

namespace Tavaan\Testing;

use Tavaan\Testing\Exceptions\AssertionFailedException;

/**
 * TestCase — کلاس پایه برای تست‌های خودنوشته Tavaan.
 *
 * 🔒 Native runner: هیچ وابستگی به PHPUnit ندارد.
 * سازگار با PHPUnit: اگر TestCasePHPUnit موجود باشد، می‌توان از PHPUnit هم استفاده کرد.
 *
 * @see docs/03-engines/test.md (DOC-084E)
 */
abstract class TestCase
{
    /**
     * @var list<array{type: string, message: string, expected: mixed, actual: mixed}>
     */
    private array $failures = [];

    /**
     * @var class-string<\Throwable>|null
     */
    private ?string $expectedException = null;
    private ?string $expectedExceptionMessage = null;
    private ?int $expectedExceptionCode = null;

    /**
     * setUp — اجرا قبل از هر تست. Override در subclasses.
     */
    protected function setUp(): void
    {
    }

    /**
     * tearDown — اجرا بعد از هر تست. Override در subclasses.
     */
    protected function tearDown(): void
    {
    }

    /**
     * اجرای یک تست با مدیریت setUp/tearDown و exception expectations.
     *
     * @param string $name نام متد تست
     */
    public function run(string $name): TestResult
    {
        $result = new TestResult($name, $this::class);
        $this->setUp();
        try {
            $reflection = new \ReflectionMethod($this, $name);
            $reflection->invoke($this);
            if ($this->expectedException !== null) {
                $result->fail(
                    "Expected exception {$this->expectedException} was not thrown."
                );
            } else {
                $result->pass();
            }
        } catch (AssertionFailedException $e) {
            $result->fail($e->getMessage());
        } catch (\Throwable $e) {
            if ($this->expectedException !== null
                && $e instanceof $this->expectedException) {
                if ($this->expectedExceptionMessage !== null
                    && !\str_contains($e->getMessage(), $this->expectedExceptionMessage)) {
                    $result->fail("Expected exception message '{$this->expectedExceptionMessage}' not found in '{$e->getMessage()}'.");
                } elseif ($this->expectedExceptionCode !== null
                          && $e->getCode() !== $this->expectedExceptionCode) {
                    $result->fail("Expected exception code {$this->expectedExceptionCode}, got {$e->getCode()}.");
                } else {
                    $result->pass();
                }
            } else {
                $result->fail("Unhandled " . \get_class($e) . ": " . $e->getMessage());
            }
        } finally {
            $this->tearDown();
            $this->expectedException = null;
            $this->expectedExceptionMessage = null;
            $this->expectedExceptionCode = null;
        }
        return $result;
    }

    /**
     * @param class-string<\Throwable> $exception
     */
    public function expectException(string $exception): void
    {
        $this->expectedException = $exception;
    }

    public function expectExceptionMessage(string $message): void
    {
        $this->expectedExceptionMessage = $message;
    }

    public function expectExceptionCode(int $code): void
    {
        $this->expectedExceptionCode = $code;
    }

    // === Assertion methods ===

    public static function assertTrue(mixed $cond, string $message = ''): void
    {
        if ($cond !== true) {
            throw AssertionFailedException::forAssertion('assertTrue', $message ?: 'Expected true, got ' . \var_export($cond, true));
        }
    }

    public static function assertFalse(mixed $cond, string $message = ''): void
    {
        if ($cond !== false) {
            throw AssertionFailedException::forAssertion('assertFalse', $message ?: 'Expected false, got ' . \var_export($cond, true));
        }
    }

    public static function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            throw AssertionFailedException::forAssertion('assertSame', $message ?: "Expected " . \var_export($expected, true) . ", got " . \var_export($actual, true));
        }
    }

    public static function assertNotSame(mixed $a, mixed $b, string $message = ''): void
    {
        if ($a === $b) {
            throw AssertionFailedException::forAssertion('assertNotSame', $message ?: 'Expected different values');
        }
    }

    public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected != $actual) {
            throw AssertionFailedException::forAssertion('assertEquals', $message ?: "Expected " . \var_export($expected, true) . ", got " . \var_export($actual, true));
        }
    }

    public static function assertNull(mixed $v, string $message = ''): void
    {
        if ($v !== null) {
            throw AssertionFailedException::forAssertion('assertNull', $message ?: 'Expected null, got ' . \var_export($v, true));
        }
    }

    public static function assertNotNull(mixed $v, string $message = ''): void
    {
        if ($v === null) {
            throw AssertionFailedException::forAssertion('assertNotNull', $message ?: 'Expected not null');
        }
    }

    public static function assertCount(int $expected, mixed $haystack, string $message = ''): void
    {
        $actual = \is_countable($haystack) ? \count($haystack) : null;
        if ($actual === null) {
            throw AssertionFailedException::forAssertion('assertCount', $message ?: 'Value is not countable');
        }
        if ($actual !== $expected) {
            throw AssertionFailedException::forAssertion('assertCount', $message ?: "Expected count {$expected}, got {$actual}");
        }
    }

    public static function assertContains(mixed $needle, mixed $haystack, string $message = ''): void
    {
        if (\is_array($haystack)) {
            if (!\in_array($needle, $haystack, true)) {
                throw AssertionFailedException::forAssertion('assertContains', $message ?: 'Needle not found in array');
            }
        } elseif (\is_string($haystack)) {
            if (!\str_contains($haystack, (string) $needle)) {
                throw AssertionFailedException::forAssertion('assertContains', $message ?: 'Needle not found in string');
            }
        } elseif ($haystack instanceof \Traversable) {
            $found = false;
            foreach ($haystack as $v) {
                if ($v === $needle) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw AssertionFailedException::forAssertion('assertContains', $message ?: 'Needle not found in traversable');
            }
        } else {
            throw AssertionFailedException::forAssertion('assertContains', $message ?: 'Haystack must be array, string, or Traversable');
        }
    }

    public static function assertArrayHasKey(string|int $key, array $arr, string $message = ''): void
    {
        if (!\array_key_exists($key, $arr)) {
            throw AssertionFailedException::forAssertion('assertArrayHasKey', $message ?: "Key '{$key}' not in array");
        }
    }

    public static function assertInstanceOf(string $expected, mixed $actual, string $message = ''): void
    {
        if (!($actual instanceof $expected)) {
            $actualClass = \is_object($actual) ? \get_class($actual) : \gettype($actual);
            throw AssertionFailedException::forAssertion('assertInstanceOf', $message ?: "Expected instance of {$expected}, got {$actualClass}");
        }
    }

    public static function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
    {
        if (!\str_contains($haystack, $needle)) {
            throw AssertionFailedException::forAssertion('assertStringContainsString', $message ?: "String '{$needle}' not in '{$haystack}'");
        }
    }

    public static function assertMatchesRegex(string $pattern, string $subject, string $message = ''): void
    {
        if (\preg_match($pattern, $subject) !== 1) {
            throw AssertionFailedException::forAssertion('assertMatchesRegex', $message ?: "Pattern {$pattern} does not match '{$subject}'");
        }
    }

    public static function assertGreaterThan(mixed $expected, mixed $actual, string $message = ''): void
    {
        if (!($actual > $expected)) {
            throw AssertionFailedException::forAssertion('assertGreaterThan', $message ?: "Expected > {$expected}, got {$actual}");
        }
    }

    public static function assertLessThan(mixed $expected, mixed $actual, string $message = ''): void
    {
        if (!($actual < $expected)) {
            throw AssertionFailedException::forAssertion('assertLessThan', $message ?: "Expected < {$expected}, got {$actual}");
        }
    }

    public static function assertGreaterThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void
    {
        if (!($actual >= $expected)) {
            throw AssertionFailedException::forAssertion('assertGreaterThanOrEqual', $message ?: "Expected >= {$expected}, got {$actual}");
        }
    }

    public static function assertEqualsWithDelta(mixed $expected, mixed $actual, float $delta, string $message = ''): void
    {
        if (\abs((float) $expected - (float) $actual) > $delta) {
            throw AssertionFailedException::forAssertion('assertEqualsWithDelta', $message ?: "Expected {$expected} ± {$delta}, got {$actual}");
        }
    }

    public static function assertEmpty(mixed $v, string $message = ''): void
    {
        if (!empty($v)) {
            throw AssertionFailedException::forAssertion('assertEmpty', $message ?: 'Expected empty value');
        }
    }

    public static function assertNotEmpty(mixed $v, string $message = ''): void
    {
        if (empty($v)) {
            throw AssertionFailedException::forAssertion('assertNotEmpty', $message ?: 'Expected not empty value');
        }
    }

    public static function fail(string $message = ''): void
    {
        throw AssertionFailedException::forAssertion('fail', $message ?: 'Test explicitly failed');
    }

    public static function markTestIncomplete(string $message = ''): void
    {
        throw new \Tavaan\Testing\Exceptions\TestIncompleteException($message ?: 'Test is incomplete.');
    }

    /**
     * ...
     */
    /**
     * actingAs
     */
    public function actingAs(/* ... */): mixed
    {
        $this->user = $user; return $this;
    }
    /**
     * get
     */
    public function get(/* ... */): mixed
    {
        return $this->call("GET", $uri);
    }
    /**
     * post
     */
    public function post(/* ... */): mixed
    {
        return $this->call("POST", $uri, $data);
    }
    /**
     * postJson
     */
    public function postJson(/* ... */): mixed
    {
        return $this->call("POST", $uri, $data, true);
    }

    /**
     * ...
     */

    /**
     * ...
     */

    /**
     * ...
     */

    /**
     * ...
     */

    /**
     * ...
     */

    /**
     * ...
     */

    /**
     * ...
     */

    /**
     * ...
     */

    public static function assertFileExists(string $path, string $message = ''): void
    {
        if (!file_exists($path)) {
            throw AssertionFailedException::forAssertion('assertFileExists', $message ?: "File '{$path}' does not exist.");
        }
    }

    public static function assertFileNotExists(string $path, string $message = ''): void
    {
        if (file_exists($path)) {
            throw AssertionFailedException::forAssertion('assertFileNotExists', $message ?: "File '{$path}' exists.");
        }
    }

    public static function assertDirectoryExists(string $path, string $message = ''): void
    {
        if (!is_dir($path)) {
            throw AssertionFailedException::forAssertion('assertDirectoryExists', $message ?: "Directory '{$path}' does not exist.");
        }
    }

    public static function assertStringStartsWith(string $needle, string $haystack, string $message = ''): void
    {
        if (!str_starts_with($haystack, $needle)) {
            throw AssertionFailedException::forAssertion('assertStringStartsWith', $message ?: "'{$haystack}' does not start with '{$needle}'.");
        }
    }

    public static function assertStringEndsWith(string $needle, string $haystack, string $message = ''): void
    {
        if (!str_ends_with($haystack, $needle)) {
            throw AssertionFailedException::forAssertion('assertStringEndsWith', $message ?: "'{$haystack}' does not end with '{$needle}'.");
        }
    }

    public static function assertIsArray(mixed $value, string $message = ''): void
    {
        if (!is_array($value)) {
            throw AssertionFailedException::forAssertion('assertIsArray', $message ?: 'Expected array.');
        }
    }

    public static function assertIsNotArray(mixed $value, string $message = ''): void
    {
        if (is_array($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotArray', $message ?: 'Expected not array.');
        }
    }

    public static function assertIsString(mixed $value, string $message = ''): void
    {
        if (!is_string($value)) {
            throw AssertionFailedException::forAssertion('assertIsString', $message ?: 'Expected string.');
        }
    }

    public static function assertIsNotString(mixed $value, string $message = ''): void
    {
        if (is_string($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotString', $message ?: 'Expected not string.');
        }
    }

    public static function assertIsInt(mixed $value, string $message = ''): void
    {
        if (!is_int($value)) {
            throw AssertionFailedException::forAssertion('assertIsInt', $message ?: 'Expected int.');
        }
    }

    public static function assertIsNotInt(mixed $value, string $message = ''): void
    {
        if (is_int($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotInt', $message ?: 'Expected not int.');
        }
    }

    public static function assertIsBool(mixed $value, string $message = ''): void
    {
        if (!is_bool($value)) {
            throw AssertionFailedException::forAssertion('assertIsBool', $message ?: 'Expected bool.');
        }
    }

    public static function assertIsNotBool(mixed $value, string $message = ''): void
    {
        if (is_bool($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotBool', $message ?: 'Expected not bool.');
        }
    }

    public static function assertIsFloat(mixed $value, string $message = ''): void
    {
        if (!is_float($value)) {
            throw AssertionFailedException::forAssertion('assertIsFloat', $message ?: 'Expected float.');
        }
    }

    public static function assertIsNotFloat(mixed $value, string $message = ''): void
    {
        if (is_float($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotFloat', $message ?: 'Expected not float.');
        }
    }

    public static function assertIsObject(mixed $value, string $message = ''): void
    {
        if (!is_object($value)) {
            throw AssertionFailedException::forAssertion('assertIsObject', $message ?: 'Expected object.');
        }
    }

    public static function assertIsNotObject(mixed $value, string $message = ''): void
    {
        if (is_object($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotObject', $message ?: 'Expected not object.');
        }
    }

    public static function assertIsCallable(mixed $value, string $message = ''): void
    {
        if (!is_callable($value)) {
            throw AssertionFailedException::forAssertion('assertIsCallable', $message ?: 'Expected callable.');
        }
    }

    public static function assertIsNotCallable(mixed $value, string $message = ''): void
    {
        if (is_callable($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotCallable', $message ?: 'Expected not callable.');
        }
    }

    public static function assertIsResource(mixed $value, string $message = ''): void
    {
        if (!is_resource($value)) {
            throw AssertionFailedException::forAssertion('assertIsResource', $message ?: 'Expected resource.');
        }
    }

    public static function assertIsNotResource(mixed $value, string $message = ''): void
    {
        if (is_resource($value)) {
            throw AssertionFailedException::forAssertion('assertIsNotResource', $message ?: 'Expected not resource.');
        }
    }

    public static function assertNotContains(mixed $needle, mixed $haystack, string $message = ''): void
    {
        if (is_array($haystack)) {
            if (in_array($needle, $haystack, true)) {
                throw AssertionFailedException::forAssertion('assertNotContains', $message ?: 'Needle found in array.');
            }
        } elseif (is_string($haystack)) {
            if (str_contains($haystack, (string) $needle)) {
                throw AssertionFailedException::forAssertion('assertNotContains', $message ?: 'Needle found in string.');
            }
        }
    }

    public static function assertNotTrue(mixed $value, string $message = ''): void
    {
        if ($value === true) {
            throw AssertionFailedException::forAssertion('assertNotTrue', $message ?: 'Expected not true.');
        }
    }

    public static function assertNotFalse(mixed $value, string $message = ''): void
    {
        if ($value === false) {
            throw AssertionFailedException::forAssertion('assertNotFalse', $message ?: 'Expected not false.');
        }
    }

    public static function assertNotEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected == $actual) {
            throw AssertionFailedException::forAssertion('assertNotEquals', $message ?: 'Expected not equal.');
        }
    }

    public static function assertNotCount(int $expected, mixed $haystack, string $message = ''): void
    {
        $actual = is_countable($haystack) ? count($haystack) : 0;
        if ($actual === $expected) {
            throw AssertionFailedException::forAssertion('assertNotCount', $message ?: "Expected count != {$expected}, got {$actual}.");
        }
    }

    public static function assertNotInstanceOf(string $expected, mixed $actual, string $message = ''): void
    {
        if ($actual instanceof $expected) {
            throw AssertionFailedException::forAssertion('assertNotInstanceOf', $message ?: 'Expected not instance of ' . $expected);
        }
    }

    public static function assertArrayNotHasKey(string|int $key, array $arr, string $message = ''): void
    {
        if (array_key_exists($key, $arr)) {
            throw AssertionFailedException::forAssertion('assertArrayNotHasKey', $message ?: "Key '{$key}' found in array.");
        }
    }

    public static function assertStringNotContainsString(string $needle, string $haystack, string $message = ''): void
    {
        if (str_contains($haystack, $needle)) {
            throw AssertionFailedException::forAssertion('assertStringNotContainsString', $message ?: "String '{$needle}' found in '{$haystack}'.");
        }
    }

    public static function assertStringNotStartsWith(string $needle, string $haystack, string $message = ''): void
    {
        if (str_starts_with($haystack, $needle)) {
            throw AssertionFailedException::forAssertion('assertStringNotStartsWith', $message ?: "String '{$haystack}' starts with '{$needle}'.");
        }
    }

    public static function assertStringNotEndsWith(string $needle, string $haystack, string $message = ''): void
    {
        if (str_ends_with($haystack, $needle)) {
            throw AssertionFailedException::forAssertion('assertStringNotEndsWith', $message ?: "String '{$haystack}' ends with '{$needle}'.");
        }
    }

    public static function assertLessThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void
    {
        if (!($actual <= $expected)) {
            throw AssertionFailedException::forAssertion('assertLessThanOrEqual', $message ?: "Expected <= {$expected}, got {$actual}.");
        }
    }


    public function createMock(string $class): object
    {
        return Mock\Mock::of($class)->getMock();
    }

    public function createStub(string $class): object
    {
        return Mock\Mock::of($class)->getMock();
    }

    public function createMockForAbstractClass(string $class): object
    {
        return Mock\Mock::of($class)->getMock();
    }

    public function expectExceptionMessageMatches(string $pattern): void
    {
        $this->expectedExceptionMessagePattern = $pattern;
    }

    private ?string $expectedExceptionMessagePattern = null;

}
