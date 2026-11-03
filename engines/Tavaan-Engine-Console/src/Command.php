<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console;

/**
 * Command — کلاس پایه برای commandهای CLI.
 *
 * @see docs/03-engines/console.md
 */
abstract class Command
{
    /** @var array<string, mixed> */
    private array $arguments = [];

    /** @var array<string, mixed> */
    private array $options = [];

    /** @var array<string, callable> */
    private array $handlers = [];

    /** @var list<string> */
    private array $allowedArgs = [];

    /** @var list<string> */
    private array $allowedOptions = [];

    protected const SUCCESS = 0;
    protected const FAILURE = 1;

    public function name(): string
    {
        return static::NAME ?? '';
    }

    public function description(): string
    {
        return static::DESCRIPTION ?? '';
    }

    /**
     * @param array<string, mixed> $arguments
     * @param array<string, mixed> $options
     */
    public function configure(array $arguments, array $options): void
    {
        $this->arguments = $arguments;
        $this->options = $options;
    }

    public function argument(string $name, mixed $default = null): mixed
    {
        return $this->arguments[$name] ?? $default;
    }

    public function option(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function hasOption(string $name): bool
    {
        return \array_key_exists($name, $this->options);
    }

    public function hasArgument(string $name): bool
    {
        return \array_key_exists($name, $this->arguments);
    }

    /**
     * @return array<string, mixed>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return array<string, mixed>
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $args
     * @param array<string, mixed> $options
     */
    public function run(array $args = [], array $options = []): int
    {
        $this->configure($args, $options);
        return $this->handle();
    }

    abstract public function handle(): int;

    // === Output methods ===

    public function info(string $message): void
    {
        $this->line("\033[32m{$message}\033[0m");
    }

    public function error(string $message): void
    {
        $this->line("\033[31m{$message}\033[0m");
    }

    public function warn(string $message): void
    {
        $this->line("\033[33m{$message}\033[0m");
    }

    public function comment(string $message): void
    {
        $this->line("\033[90m{$message}\033[0m");
    }

    public function line(string $message = ''): void
    {
        echo $message . "
";
    }

    public function newLine(int $count = 1): void
    {
        echo \str_repeat("
", $count);
    }

    public function alert(string $message): void
    {
        $this->newLine();
        $this->line(\str_repeat('!', \strlen($message) + 4));
        $this->line('! ' . $message . ' !');
        $this->line(\str_repeat('!', \strlen($message) + 4));
        $this->newLine();
    }

    /**
     * @param list<string> $headers
     * @param list<list<string>> $rows
     */
    public function table(array $headers, array $rows): void
    {
        $widths = [];
        foreach ($headers as $i => $h) {
            $widths[$i] = \strlen($h);
        }
        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $widths[$i] = \max($widths[$i] ?? 0, \strlen((string) $cell));
            }
        }
        // Headers
        $this->printRow($headers, $widths);
        $this->line(\str_repeat('-', \array_sum($widths) + \count($widths) * 3 + 1));
        // Rows
        foreach ($rows as $row) {
            $this->printRow($row, $widths);
        }
    }

    /**
     * @param list<string> $row
     * @param list<int> $widths
     */
    private function printRow(array $row, array $widths): void
    {
        $cells = [];
        foreach ($row as $i => $cell) {
            $cells[] = \str_pad((string) $cell, $widths[$i] ?? 0);
        }
        $this->line('| ' . \implode(' | ', $cells) . ' |');
    }

    // === Input methods ===

    public function ask(string $question, ?string $default = null): ?string
    {
        $defaultStr = $default !== null ? " [{$default}]" : '';
        echo "{$question}{$defaultStr}: ";
        $line = \fgets(\STDIN);
        $line = $line === false ? '' : \trim($line);
        return $line === '' ? $default : $line;
    }

    public function secret(string $question): ?string
    {
        echo "{$question}: ";
        // Hide input — system-specific; fall back to plain
        if (\PHP_OS_FAMILY === 'Linux' || \PHP_OS_FAMILY === 'Darwin') {
            \system('stty -echo');
        }
        $line = \fgets(\STDIN);
        if (\PHP_OS_FAMILY === 'Linux' || \PHP_OS_FAMILY === 'Darwin') {
            \system('stty echo');
        }
        echo "
";
        return $line === false ? null : \trim($line);
    }

    public function confirm(string $question, bool $default = false): bool
    {
        $defaultStr = $default ? 'Y/n' : 'y/N';
        echo "{$question} ({$defaultStr}): ";
        $line = \fgets(\STDIN);
        $line = $line === false ? '' : \strtolower(\trim($line));
        if ($line === '') return $default;
        return \in_array($line, ['y', 'yes'], true);
    }

    /**
     * @param list<string> $choices
     */
    public function choice(string $question, array $choices, ?string $default = null): ?string
    {
        foreach ($choices as $i => $c) {
            echo "  [{$i}] {$c}
";
        }
        $defaultIdx = $default !== null ? \array_search($default, $choices, true) : false;
        $defaultStr = $defaultIdx !== false ? " [{$defaultIdx}]" : '';
        echo "{$question}{$defaultStr}: ";
        $line = \fgets(\STDIN);
        $line = $line === false ? '' : \trim($line);
        if ($line === '') return $default;
        if (\is_numeric($line) && isset($choices[(int) $line])) {
            return $choices[(int) $line];
        }
        return \in_array($line, $choices, true) ? $line : $default;
    }

    public function progressStart(int $total = 100): void
    {
        $this->progressTotal = $total;
        $this->progressCurrent = 0;
        $this->progressStartTime = microtime(true);
        echo "  0/{$total} (  0%)";
    }

    public function progressAdvance(int $step = 1): void
    {
        $this->progressCurrent = min($this->progressTotal, ($this->progressCurrent ?? 0) + $step);
        $pct = $this->progressTotal > 0 ? (int) round(($this->progressCurrent / $this->progressTotal) * 100) : 100;
        echo "\r  {$this->progressCurrent}/{$this->progressTotal} ({$pct}%)   ";
        if ($this->progressCurrent >= $this->progressTotal) {
            echo "\n";
        }
    }

    public function progressFinish(): void
    {
        if (isset($this->progressTotal) && ($this->progressCurrent ?? 0) < $this->progressTotal) {
            $this->progressCurrent = $this->progressTotal;
            echo "\r  {$this->progressTotal}/{$this->progressTotal} (100%)\n";
        }
    }

    /** @var int */
    private int $progressTotal = 0;
    /** @var int */
    private int $progressCurrent = 0;
    /** @var float */
    private float $progressStartTime = 0.0;
}
