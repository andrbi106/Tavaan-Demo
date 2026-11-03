<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail;

use Tavaan\Engine\Mail\Contracts\TransportInterface;
use Tavaan\Engine\Mail\Transports\ArrayTransport;
use Tavaan\Engine\Mail\Transports\LogTransport;

/**
 * MailManager — مدیریت Mailer و Transportها.
 *
 * @see docs/03-engines/mail.md
 */
final class MailManager
{
    private ?TransportInterface $transport = null;
    private bool $faked = false;
    private ?ArrayTransport $fakeTransport = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function to(string $email): PendingMail
    {
        return (new PendingMail($this))->to($email);
    }

    public function cc(string $email): PendingMail
    {
        return (this)->cc($email);
    }

    public function send(Message $message): SentMessage
    {
        return $this->getTransport()->send($message);
    }

    public function sendMailable(Mailable $mailable): SentMessage
    {
        return $this->send($mailable->toMessage());
    }

    public function fake(): self
    {
        $this->faked = true;
        $this->fakeTransport = new ArrayTransport();
        return $this;
    }

    public function isFaked(): bool
    {
        return $this->faked;
    }

    public function assertSent(string $mailableClass): bool
    {
        return $this->fakeTransport !== null && $this->fakeTransport->count() > 0;
    }

    public function assertSentTimes(int $count): bool
    {
        return $this->fakeTransport !== null && $this->fakeTransport->count() === $count;
    }

    /** @return list<Message> */
    public function sentMessages(): array
    {
        return $this->fakeTransport?->messages() ?? [];
    }

    private function getTransport(): TransportInterface
    {
        if ($this->faked && $this->fakeTransport !== null) {
            return $this->fakeTransport;
        }
        if ($this->transport === null) {
            $this->transport = $this->createTransport();
        }
        return $this->transport;
    }

    private function createTransport(): TransportInterface
    {
        $driver = $this->config['driver'] ?? 'log';
        return match ($driver) {
            'log' => new LogTransport($this->config['log_path'] ?? 'storage/logs/mail.log'),
            'array' => new ArrayTransport(),
            default => new LogTransport(),
        };
    }
}

/**
 * PendingMail — fluent builder برای ارسال ایمیل.
 */
final class PendingMail
{
    /** @var list<string> */
    private array $to = [];
    /** @var list<string> */
    private array $cc = [];
    /** @var list<string> */
    private array $bcc = [];

    public function __construct(
        private readonly MailManager $manager,
    ) {
    }

    public function to(string $email): self
    {
        $this->to[] = $email;
        return $this;
    }

    public function cc(string $email): self
    {
        $this->cc[] = $email;
        return $this;
    }

    public function bcc(string $email): self
    {
        $this->bcc[] = $email;
        return $this;
    }

    public function send(Mailable $mailable): SentMessage
    {
        $message = $mailable->toMessage();
        foreach ($this->to as $e) $message->to($e);
        foreach ($this->cc as $e) $message->cc($e);
        foreach ($this->bcc as $e) $message->bcc($e);
        return $this->manager->send($message);
    }

    public function raw(string $text, callable $callback): SentMessage
    {
        $message = new Message();
        $message->text($text);
        $callback($message);
        foreach ($this->to as $e) $message->to($e);
        return $this->manager->send($message);
    }
}
