<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log;

/**
 * LogLevel — ۸ سطح RFC 5424.
 *
 * @see docs/03-engines/log.md
 */
enum LogLevel: int
{
    case Emergency = 0;
    case Alert = 1;
    case Critical = 2;
    case Error = 3;
    case Warning = 4;
    case Notice = 5;
    case Info = 6;
    case Debug = 7;

    public static function fromString(string $name): self
    {
        return \ucfirst(\strtolower($name)) === 'Emergency' ? self::Emergency
            : (\ucfirst(\strtolower($name)) === 'Alert' ? self::Alert
            : (\ucfirst(\strtolower($name)) === 'Critical' ? self::Critical
            : (\ucfirst(\strtolower($name)) === 'Error' ? self::Error
            : (\ucfirst(\strtolower($name)) === 'Warning' ? self::Warning
            : (\ucfirst(\strtolower($name)) === 'Notice' ? self::Notice
            : (\ucfirst(\strtolower($name)) === 'Info' ? self::Info
            : self::Debug))))));
    }

    public function name(): string
    {
        return match ($this) {
            self::Emergency => 'EMERGENCY',
            self::Alert => 'ALERT',
            self::Critical => 'CRITICAL',
            self::Error => 'ERROR',
            self::Warning => 'WARNING',
            self::Notice => 'NOTICE',
            self::Info => 'INFO',
            self::Debug => 'DEBUG',
        };
    }

    public function includes(self $other): bool
    {
        return $other->value <= $this->value;
    }
}
