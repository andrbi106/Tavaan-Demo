<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail\Transports;

use Tavaan\Engine\Mail\Contracts\TransportInterface;
use Tavaan\Engine\Mail\Message;
use Tavaan\Engine\Mail\SentMessage;

/**
 * ArrayTransport — ذخیره در حافظه (برای تست).
 */
final class ArrayTransport implements TransportInterface
{
    /** @var list<Message> */
    private array $sent = [];

    public function send(Message $message): SentMessage
    {
        $this->sent[] = $message;
        return new SentMessage($message, \bin2hex(\random_bytes(16)));
    }

    /** @return list<Message> */
    public function messages(): array
    {
        return $this->sent;
    }

    public function count(): int
    {
        return \count($this->sent);
    }

    public function reset(): void
    {
        $this->sent = [];
    }
}

/**
 * LogTransport — نوشتن ایمیل در لاگ (برای dev).
 */
final class LogTransport implements TransportInterface
{
    public function __construct(
        private readonly string $logPath = 'storage/logs/mail.log',
    ) {
    }

    public function send(Message $message): SentMessage
    {
        $line = \sprintf(
            "[%s] To: %s | Subject: %s
",
            \date('Y-m-d H:i:s'),
            \implode(', ', $message->getTo()),
            $message->getSubject() ?? ''
        );
        $dir = \dirname($this->logPath);
        if (!\is_dir($dir)) \mkdir($dir, 0777, true);
        @\file_put_contents($this->logPath, $line, \FILE_APPEND);
        return new SentMessage($message, 'logged');
    }
}
