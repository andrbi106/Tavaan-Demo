<?php

declare(strict_types=1);

namespace Tavaan\Testing;

/**
 * Command `test` — اجرای تمام تست‌ها از مسیر داده‌شده.
 */
final class TestCommand
{
    public function name(): string
    {
        return 'test';
    }

    public function description(): string
    {
        return 'اجرای چارچوب تست Tavaan (Native runner).';
    }

    public function handle(array $args = []): int
    {
        $dir = $args[0] ?? 'tests';
        if (!\is_dir($dir)) {
            echo "Test directory not found: {$dir}
";
            return 1;
        }
        echo "Running tests in: {$dir}
";
        $result = TestRunner::runDirectory($dir, verbose: true);
        echo "
" . $result->summary() . "
";
        return $result->isPassed() ? 0 : 1;
    }
}
