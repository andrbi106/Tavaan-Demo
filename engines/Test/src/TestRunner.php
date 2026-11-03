<?php

declare(strict_types=1);

namespace Tavaan\Testing;

/**
 * TestRunner — اجرای TestCase‌ها و گزارش نتایج.
 *
 * 🔒 Native runner: بدون PHPUnit.
 */
final class TestRunner
{
    /**
     * @param list<TestCase> $cases
     */
    public function __construct(
        private readonly array $cases = [],
        private readonly bool $verbose = false,
    ) {
    }

    /**
     * اجرای تمام TestCase‌ها.
     *
     * @return TestSuiteResult
     */
    public function run(): TestSuiteResult
    {
        $results = [];
        $total = 0;
        $passed = 0;
        $failed = 0;
        foreach ($this->cases as $case) {
            $reflection = new \ReflectionClass($case);
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $name = $method->getName();
                if (!\str_starts_with($name, 'test')) {
                    continue;
                }
                if ($name === 'run' || $name === 'setUp' || $name === 'tearDown') {
                    continue;
                }
                $total++;
                $start = \microtime(true);
                $result = $case->run($name);
                $result->durationMs = (\microtime(true) - $start) * 1000;
                $results[] = $result;
                if ($result->isPassed()) {
                    $passed++;
                } else {
                    $failed++;
                }
                if ($this->verbose) {
                    echo $result . "
";
                }
            }
        }
        return new TestSuiteResult(total: $total, passed: $passed, failed: $failed, results: $results);
    }

    /**
     * اجرای تمام TestCase‌ها از یک دایرکتوری.
     *
     * @param string $dir مسیر دایرکتوری tests
     */
    public static function runDirectory(string $dir, bool $verbose = false): TestSuiteResult
    {
        $cases = [];
        foreach (\glob("{$dir}/**/*Test.php") as $file) {
            require_once $file;
            // Extract class name from file
            $contents = \file_get_contents($file);
            if (\preg_match('/^namespace\s+([\w\\\\]+);/m', (string) $contents, $m)) {
                $ns = $m[1];
            } else {
                $ns = '';
            }
            $basename = \basename($file, '.php');
            $cls = $ns ? "{$ns}\\{$basename}" : $basename;
            if (\class_exists($cls)) {
                $cases[] = new $cls();
            }
        }
        $runner = new self($cases, $verbose);
        return $runner->run();
    }

    /**
     * result — return last suite result (or null if not run yet).
     */
    public function result(): ?TestSuiteResult
    {
        return $this->lastResult ?? null;
    }
    /**
     * suite — return list of registered TestCase instances.
     * @return list<TestCase>
     */
    public function suite(): array
    {
        return $this->cases;
    }
}
