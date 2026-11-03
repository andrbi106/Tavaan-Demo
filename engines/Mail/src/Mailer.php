<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail;

final class Mailer
{
    private array $sent = [];

    public function __construct(private readonly array $config = []) {}

    public function send(Mailable $mailable): SentMessage
    {
        $message = $mailable->toMessage();
        $this->sent[] = $message;
        return new SentMessage($message);
    }

    

    public function queue(Mailable $mailable): self
    {
        return $this;
    }

    public function later(int $delay, Mailable $mailable): self
    {
        return $this;
    }

    public function raw(string $text, callable $callback): SentMessage
    {
        $message = new Message();
        $message->text($text);
        $callback($message);
        $this->sent[] = $message;
        return new SentMessage($message);
    }

    public function to(string $email): PendingMail
    {
        return (new PendingMail($this))->to($email);
    }

    public function getSent(): array
    {
        return $this->sent;
    }

    public function sendNow(Mailable $mailable): SentMessage
    {
        return $this->send($mailable);
    }
}
