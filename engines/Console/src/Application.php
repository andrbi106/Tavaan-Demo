<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console;

/**
 * Application — ثبت و اجرای commandها.
 */
final class Application
{
    /**
     * @var array<string, Command>
     */
    private array $commands = [];

    public function add(Command $command): self
    {
        $this->commands[$command->name()] = $command;
        return $this;
    }

    public function get(string $name): ?Command
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        return \array_keys($this->commands);
    }

    /**
     * @param list<string> $argv
     */
    public function run(array $argv): int
    {
        if (empty($argv)) {
            $this->printHelp();
            return 0;
        }
        $commandName = $argv[0];
        $command = $this->commands[$commandName] ?? null;
        if ($command === null) {
            $this->error("Command not found: {$commandName}");
            $this->printHelp();
            return 1;
        }
        [$args, $options] = ArgvParser::parse(\array_slice($argv, 1));
        return $command->run($args, $options);
    }

    public function printHelp(): void
    {
        echo "Available commands:
";
        foreach ($this->commands as $name => $command) {
            $desc = $command->description();
            echo "  {$name}" . ($desc !== '' ? " — {$desc}" : '') . "
";
        }
    }
}
