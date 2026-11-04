<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log;

/**
 * LogRecord — رکورد immutable یک لاگ.
 */
final readonly class LogRecord
{
    public function __construct(
        public readonly \DateTimeImmutable $timestamp,
        public readonly LogLevel $level,
        public readonly string $message,
        public readonly array $context = [],
        public readonly string $channel = 'default',
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp->format(\DateTimeInterface::ATOM),
            'level' => $this->level->name(),
            'channel' => $this->channel,
            'message' => $this->message,
            'context' => $this->context,
        ];
    }
}
