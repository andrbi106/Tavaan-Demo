<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Contracts;

use Tavaan\Engine\Log\LogRecord;
use Tavaan\Engine\Log\LogLevel;

/**
 * قرارداد Logger.
 *
 * @api 1.0
 */
interface LoggerInterface
{
    public function emergency(string $message, array $context = []): void;
    public function alert(string $message, array $context = []): void;
    public function critical(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
    public function notice(string $message, array $context = []): void;
    public function info(string $message, array $context = []): void;
    public function debug(string $message, array $context = []): void;
    public function log(LogLevel $level, string $message, array $context = []): void;

    public function channel(string $name): self;
    public function withContext(array $context): self;
    public function shareContext(array $context): void;
    public function withoutContext(): void;
    public function flush(): void;
}
