<?php
declare(strict_types=1);
namespace Tavaan\Core\Application;

use Tavaan\Core\Events\EventDispatcher;
use Tavaan\Core\Facade\Facade;

/**
 * Builder برای ساخت Application با fluent API.
 *
 * مثال:
 *   $app = ApplicationBuilder::create(__DIR__)->boot();
 */
final class ApplicationBuilder
{
    private ?RuntimeMode $mode = null;
    private ?EventDispatcher $events = null;

    private function __construct(
        private readonly string $basePath
    ) {}

    public static function create(string $basePath): self
    {
        return new self($basePath);
    }

    public function forConsole(): self
    {
        $this->mode = RuntimeMode::Console;
        return $this;
    }

    public function forTesting(): self
    {
        $this->mode = RuntimeMode::Testing;
        return $this;
    }

    public function forDevelopment(): self
    {
        $this->mode = RuntimeMode::Development;
        return $this;
    }

    public function withEvents(EventDispatcher $dispatcher): self
    {
        $this->events = $dispatcher;
        return $this;
    }

    public function build(): Application
    {
        $events = $this->events ?? new EventDispatcher();
        $app = new Application($this->basePath, events: $events);
        $app->instance('events', $events);
        $app->instance(DispatcherInterface::class, $events);
        Facade::setContainer($app);

        if ($this->mode !== null) {
            $app->setMode($this->mode);
        }
        return $app;
    }

    public function boot(): Application
    {
        return $this->build()->boot();
    }
}
