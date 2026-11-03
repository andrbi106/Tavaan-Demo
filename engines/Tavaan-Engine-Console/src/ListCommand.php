<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console;

final class ListCommand extends Command
{
    protected const NAME = 'list';
    protected const DESCRIPTION = 'List all available commands';

    public function __construct(
        private readonly Application $app,
    ) {
    }

    public function handle(): int
    {
        $this->info('Available commands:');
        $this->newLine();
        $rows = [];
        foreach ($this->app->names() as $name) {
            $cmd = $this->app->get($name);
            $rows[] = [$name, $cmd?->description() ?? ''];
        }
        $this->table(['Command', 'Description'], $rows);
        return self::SUCCESS;
    }
}
