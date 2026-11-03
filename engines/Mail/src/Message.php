<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail;

/**
 * Message — یک پیام ایمیل.
 *
 * @see docs/03-engines/mail.md
 */
final class Message
{
    /** @var list<string> */
    private array $to = [];
    /** @var list<string> */
    private array $cc = [];
    /** @var list<string> */
    private array $bcc = [];
    private ?string $from = null;
    private ?string $fromName = null;
    /** @var list<string> */
    private array $replyTo = [];
    private ?string $subject = null;
    private ?string $htmlBody = null;
    private ?string $textBody = null;
    /** @var list<array{path: string, name: string, mime: string}> */
    private array $attachments = [];

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

    public function from(string $email, ?string $name = null): self
    {
        $this->from = $email;
        $this->fromName = $name;
        return $this;
    }

    public function replyTo(string $email): self
    {
        $this->replyTo[] = $email;
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function html(string $html): self
    {
        $this->htmlBody = $html;
        return $this;
    }

    public function text(string $text): self
    {
        $this->textBody = $text;
        return $this;
    }

    public function attach(string $path, ?string $name = null, string $mime = 'application/octet-stream'): self
    {
        $this->attachments[] = [
            'path' => $path,
            'name' => $name ?? \basename($path),
            'mime' => $mime,
        ];
        return $this;
    }

    /** @return list<string> */
    public function getTo(): array { return $this->to; }
    /** @return list<string> */
    public function getCc(): array { return $this->cc; }
    /** @return list<string> */
    public function getBcc(): array { return $this->bcc; }
    public function getFrom(): ?string { return $this->from; }
    public function getFromName(): ?string { return $this->fromName; }
    /** @return list<string> */
    public function getReplyTo(): array { return $this->replyTo; }
    public function getSubject(): ?string { return $this->subject; }
    public function getHtmlBody(): ?string { return $this->htmlBody; }
    public function getTextBody(): ?string { return $this->textBody; }
    /** @return list<array{path: string, name: string, mime: string}> */
    public function getAttachments(): array { return $this->attachments; }
}

/**
 * SentMessage — نتیجه ارسال موفق.
 */
final class SentMessage
{
    public function __construct(
        public readonly Message $message,
        public readonly string $messageId = '',
    ) {
    }
}
