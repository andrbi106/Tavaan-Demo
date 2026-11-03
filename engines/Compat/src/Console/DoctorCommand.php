<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat\Console;

/**
 * Command `doctor` — گزارش وضعیت افزونه‌ها و fallbackها.
 */
final class DoctorCommand
{
    public function name(): string
    {
        return 'doctor';
    }

    public function description(): string
    {
        return 'گزارشی از افزونه‌های PHP نصب‌شده، درایورهای فعال و fallbackهای در حال استفاده.';
    }

    /**
     * @param list<string> $args
     * @return int exit code (0 = success)
     */
    public function handle(array $args = []): int
    {
        $compat = \Tavaan\Engine\Compat\CompatServiceProvider::instance();
        $report = $compat->report();

        echo "PHP Version: " . $compat->version() . "
";
        echo "Drivers:
";
        echo "  image:   " . $compat->driver('image') . "
";
        echo "  zip:     " . $compat->driver('zip') . "
";
        echo "  http:    " . $compat->driver('http') . "
";
        echo "  math:    " . $compat->driver('math') . "
";
        echo "  unicode: " . $compat->driver('unicode') . "
";
        echo "  mime:    " . $compat->driver('mime') . "
";
        echo "
Extensions (" . \count($report) . "):
";
        foreach ($report as $ext => $info) {
            $status = $info['loaded'] ? '✅' : '❌';
            echo \sprintf("  %s %-15s fallback: %s
", $status, $ext, $info['fallback']);
        }
        return 0;
    }
}
