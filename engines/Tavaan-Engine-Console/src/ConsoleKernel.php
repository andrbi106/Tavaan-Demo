<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console;

/**
 * ConsoleKernel — orchestrates command registration and execution.
 */
final class ConsoleKernel
{
    /** @var array<string, Command> */
    private array $commands = [];

    /** @param array<string, mixed> $config */
    public function __construct(private readonly array $config = []) {}

    /**
     * handle — parse argv and dispatch to the right command.
     * @param list<string> $argv
     */
    public function handle(array $argv = []): int
    {
        $argv = $argv !== [] ? $argv : ($_SERVER['argv'] ?? []);
        // Strip script name
        array_shift($argv);
        if ($argv === []) {
            $this->listCommands();
            return 0;
        }
        $name = array_shift($argv);
        return $this->call($name, $argv);
    }

    /**
     * call — invoke a command by name with the given args.
     * @param list<string> $args
     */
    public function call(string $name, array $args = []): int
    {
        if (!isset($this->commands[$name])) {
            fwrite(STDERR, "Command not found: {$name}\n");
            return 1;
        }
        $command = $this->commands[$name];
        // Parse --flag=value and positional args
        $positional = [];
        $options = [];
        foreach ($args as $a) {
            if (str_starts_with($a, '--')) {
                $body = substr($a, 2);
                $eq = strpos($body, '=');
                if ($eq !== false) {
                    $options[substr($body, 0, $eq)] = substr($body, $eq + 1);
                } else {
                    $options[$body] = true;
                }
            } else {
                $positional[] = $a;
            }
        }
        // Map positional to argument names
        $argNames = array_keys($command->arguments());
        $argMap = [];
        $i = 0;
        foreach ($argNames as $an) {
            $argMap[$an] = $positional[$i] ?? null;
            $i++;
        }
        return $command->run($argMap, $options);
    }

    /**
     * register — add a command instance under its name.
     */
    public function register(?Command $command = null): self
    {
        if ($command !== null) {
            $this->commands[$command->name()] = $command;
        }
        return $this;
    }

    /**
     * commands — return list of registered command names.
     * @return list<string>
     */
    public function commands(): array
    {
        return array_keys($this->commands);
    }

    public function listCommands(): void
    {
        echo "Available commands:\n";
        foreach ($this->commands as $name => $cmd) {
            echo "  {$name}\t" . $cmd->description() . "\n";
        }
    }
}
