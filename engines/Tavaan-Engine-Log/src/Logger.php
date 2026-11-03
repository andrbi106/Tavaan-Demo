<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log;

use Tavaan\Engine\Log\Contracts\LoggerInterface;
use Tavaan\Engine\Log\Contracts\HandlerInterface;
use Tavaan\Engine\Log\LogLevel;
use Tavaan\Engine\Log\LogRecord;

/**
 * Logger — یک کانال لاگ.
 *
 * @see docs/03-engines/log.md
 */
final class Logger implements LoggerInterface
{
    private LogLevel $minimumLevel = LogLevel::Debug;

    /**
     * @var list<HandlerInterface>
     */
    private array $handlers = [];

    /** @var array<string, mixed> */
    private array $sharedContext = [];

    /** @var array<string, mixed> */
    private array $localContext = [];

    public function __construct(
        private readonly string $channel = 'default',
    ) {
    }

    public function setMinimumLevel(LogLevel $level): self
    {
        $this->minimumLevel = $level;
        return $this;
    }

    public function getMinimumLevel(): LogLevel
    {
        return $this->minimumLevel;
    }

    public function addHandler(HandlerInterface $handler): self
    {
        $this->handlers[] = $handler;
        return $this;
    }

    public function channel(string $name): self
    {
        $new = new self($name);
        $new->minimumLevel = $this->minimumLevel;
        $new->handlers = $this->handlers;
        $new->sharedContext = $this->sharedContext;
        return $new;
    }

    public function withContext(array $context): self
    {
        $new = clone $this;
        $new->localContext = \array_merge($this->localContext, $context);
        return $new;
    }

    public function shareContext(array $context): void
    {
        $this->sharedContext = \array_merge($this->sharedContext, $context);
    }

    public function withoutContext(): void
    {
        $this->sharedContext = [];
        $this->localContext = [];
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::Emergency, $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::Alert, $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::Critical, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::Error, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::Warning, $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::Notice, $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::Info, $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::Debug, $message, $context);
    }

    public function log(LogLevel $level, string $message, array $context = []): void
    {
        if (!$this->minimumLevel->includes($level)) {
            return;
        }
        $mergedContext = \array_merge($this->sharedContext, $this->localContext, $context);
        $record = new LogRecord(
            timestamp: new \DateTimeImmutable(),
            level: $level,
            message: $message,
            context: $mergedContext,
            channel: $this->channel,
        );
        foreach ($this->handlers as $handler) {
            $handler->handle($record);
        }
    }

    public function flush(): void
    {
        foreach ($this->handlers as $handler) {
            $handler->flush();
        }
    }

    public function close(): void
    {
        foreach ($this->handlers as $handler) {
            $handler->close();
        }
    }

    /**
     * @return list<HandlerInterface>
     */
    public function handlers(): array
    {
        return $this->handlers;
    }

    public function getName(): string
    {
        return $this->channel;
    }
}
