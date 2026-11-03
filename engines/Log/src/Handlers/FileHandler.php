<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Handlers;

use Tavaan\Engine\Log\Contracts\HandlerInterface;
use Tavaan\Engine\Log\Contracts\FormatterInterface;
use Tavaan\Engine\Log\Formatters\LineFormatter;
use Tavaan\Engine\Log\LogRecord;

/**
 * File Handler — نوشتن به فایل با چرخش روزانه اختیاری.
 */
final class FileHandler implements HandlerInterface
{
    private $fp = null;

    public function __construct(
        private readonly string $path,
        private readonly FormatterInterface $formatter = new LineFormatter(),
        private readonly bool $append = true,
    ) {
    }

    public function handle(LogRecord $record): void
    {
        $this->ensureOpen();
        $line = $this->formatter->format($record) . "
";
        \fwrite($this->fp, $line);
    }

    public function flush(): void
    {
        if ($this->fp !== null) {
            \fflush($this->fp);
        }
    }

    public function close(): void
    {
        if ($this->fp !== null && \is_resource($this->fp)) {
            \fclose($this->fp);
        }
        $this->fp = null;
    }

    private function ensureOpen(): void
    {
        if ($this->fp !== null && \is_resource($this->fp)) {
            return;
        }
        $dir = \dirname($this->path);
        if (!\is_dir($dir)) {
            \mkdir($dir, 0777, true);
        }
        $mode = $this->append ? 'a' : 'w';
        $this->fp = \fopen($this->path, $mode);
        if ($this->fp === false) {
            throw new \RuntimeException("Failed to open log file: {$this->path}");
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
