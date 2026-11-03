<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Handlers;

use Tavaan\Engine\Log\Contracts\HandlerInterface;
use Tavaan\Engine\Log\Contracts\FormatterInterface;
use Tavaan\Engine\Log\Formatters\LineFormatter;
use Tavaan\Engine\Log\LogRecord;

/**
 * Stream Handler — نوشتن به STDOUT/STDERR یا هر stream.
 */
final class StreamHandler implements HandlerInterface
{
    /**
     * @param resource $stream
     */
    public function __construct(
        private $stream,
        private readonly FormatterInterface $formatter = new LineFormatter(),
    ) {
        if (!\is_resource($stream)) {
            throw new \InvalidArgumentException('Stream must be a valid resource.');
        }
    }

    public function handle(LogRecord $record): void
    {
        $line = $this->formatter->format($record) . "
";
        \fwrite($this->stream, $line);
    }

    public function flush(): void
    {
        \fflush($this->stream);
    }

    public function close(): void
    {
        if (\is_resource($this->stream)) {
            \fclose($this->stream);
        }
    }

    /**
     * ساخت handler برای STDOUT.
     */
    public static function toStdout(FormatterInterface $formatter = new LineFormatter()): self
    {
        return new self(\STDOUT, $formatter);
    }

    /**
     * ساخت handler برای STDERR.
     */
    public static function toStderr(FormatterInterface $formatter = new LineFormatter()): self
    {
        return new self(\STDERR, $formatter);
    }
}
