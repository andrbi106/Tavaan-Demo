<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Contracts;

use Tavaan\Engine\Log\LogRecord;

/**
 * قرارداد Handler — نحوه نوشتن لاگ (file, syslog, stdout, ...).
 */
interface HandlerInterface
{
    public function handle(LogRecord $record): void;
    public function flush(): void;
    public function close(): void;
}
