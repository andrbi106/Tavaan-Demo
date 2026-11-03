<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log;

use Tavaan\Engine\Log\Contracts\LoggerInterface;
use Tavaan\Engine\Log\Contracts\HandlerInterface;
use Tavaan\Engine\Log\Handlers\FileHandler;
use Tavaan\Engine\Log\Handlers\StreamHandler;
use Tavaan\Engine\Log\Formatters\JsonFormatter;
use Tavaan\Engine\Log\Formatters\LineFormatter;
use Tavaan\Engine\Log\LogLevel;

/**
 * LogManager — مدیریت چندین کانال لاگ.
 */
final class LogManager implements LoggerInterface
{
    /**
     * @var array<string, Logger>
     */
    private array $loggers = [];

    private Logger $default;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
        $this->default = $this->createLogger($this->config['default'] ?? 'default');
    }

    public function channel(string $name): LoggerInterface
    {
        if (!isset($this->loggers[$name])) {
            $this->loggers[$name] = $this->createLogger($name);
        }
        return $this->loggers[$name];
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->default->emergency($message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->default->alert($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->default->critical($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->default->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->default->warning($message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->default->notice($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->default->info($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->default->debug($message, $context);
    }

    public function log(LogLevel $level, string $message, array $context = []): void
    {
        $this->default->log($level, $message, $context);
    }

    public function withContext(array $context): self
    {
        $new = clone $this;
        $new->default = $this->default->withContext($context);
        return $new;
    }

    public function shareContext(array $context): void
    {
        $this->default->shareContext($context);
    }

    public function withoutContext(): void
    {
        $this->default->withoutContext();
    }

    public function flush(): void
    {
        $this->default->flush();
    }

    public function getDefaultLogger(): Logger
    {
        return $this->default;
    }

    private function createLogger(string $name): Logger
    {
        $logger = new Logger($name);
        $logger->setMinimumLevel($this->parseLevel($this->config['level'] ?? 'debug'));
        // Add default handler (stdout or file)
        $handlerConfig = $this->config['handlers'] ?? [
            ['type' => 'stdout', 'format' => 'line'],
        ];
        foreach ($handlerConfig as $cfg) {
            $handler = $this->createHandler($cfg, $name);
            if ($handler !== null) {
                $logger->addHandler($handler);
            }
        }
        return $logger;
    }

    private function parseLevel(string $level): LogLevel
    {
        return LogLevel::fromString($level);
    }

    /**
     * @param array<string, mixed> $cfg
     */
    private function createHandler(array $cfg, string $channel): ?HandlerInterface
    {
        $type = $cfg['type'] ?? 'stdout';
        $format = $cfg['format'] ?? 'line';
        $formatter = $format === 'json' ? new JsonFormatter() : new LineFormatter();
        return match ($type) {
            'stdout' => StreamHandler::toStdout($formatter),
            'stderr' => StreamHandler::toStderr($formatter),
            'file' => new FileHandler(
                $cfg['path'] ?? "/tmp/{$channel}.log",
                $formatter,
                $cfg['append'] ?? true,
            ),
            default => null,
        };
    }

    /**
     * driver — return named logger (defaults to default channel).
     */
    public function driver(?string $name = null): LoggerInterface
    {
        if ($name === null) {
            return $this->default;
        }
        return $this->channel($name);
    }
    /**
     * extend — register a custom driver creator.
     * @param string $name
     * @param callable(string, array): LoggerInterface $callback
     */
    public function extend(string $name, callable $callback): self
    {
        $this->customCreators[$name] = $callback;
        return $this;
    }
    /**
     * getChannels
     */
    public function getChannels()
    {
        return $this->config["channels"] ?? null;
    }
    /**
     * stack — create a stacked logger that writes to multiple channels.
     * @param array<int, string> $channels
     */
    public function stack(array $channels, string $name = 'stack'): LoggerInterface
    {
        $loggers = [];
        foreach ($channels as $channel) {
            $loggers[] = $this->channel($channel);
        }
        return new class($loggers, $name) implements LoggerInterface {
            public function __construct(private array $loggers, private string $name) {}

            public function emergency(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->emergency($message, $context); }
            }
            public function alert(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->alert($message, $context); }
            }
            public function critical(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->critical($message, $context); }
            }
            public function error(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->error($message, $context); }
            }
            public function warning(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->warning($message, $context); }
            }
            public function notice(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->notice($message, $context); }
            }
            public function info(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->info($message, $context); }
            }
            public function debug(string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->debug($message, $context); }
            }
            public function log(LogLevel $level, string $message, array $context = []): void
            {
                foreach ($this->loggers as $logger) { $logger->log($level, $message, $context); }
            }
            public function withContext(array $context): self { return $this; }
            public function shareContext(array $context): void {}
            public function withoutContext(): void {}
            public function flush(): void
            {
                foreach ($this->loggers as $logger) { $logger->flush(); }
            }
        };
    }
}
