<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail;

abstract class Mailable
{
    protected ?string $subject = null;
    protected ?string $view = null;
    protected array $viewData = [];
    protected array $to = [];
    protected ?string $from = null;
    protected array $attachments = [];

    abstract public function build(): self;

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function view(string $view, array $data = []): self
    {
        $this->view = $view;
        $this->viewData = $data;
        return $this;
    }

    public function to(string $email): self
    {
        $this->to[] = $email;
        return $this;
    }

    public function from(string $email): self
    {
        $this->from = $email;
        return $this;
    }

    public function attach(string $path, ?string $name = null, string $mime = 'application/octet-stream'): self
    {
        $this->attachments[] = ['path' => $path, 'name' => $name ?? basename($path), 'mime' => $mime];
        return $this;
    }

    public function getSubject(): ?string { return $this->subject; }
    public function getView(): ?string { return $this->view; }
    public function getViewData(): array { return $this->viewData; }
    public function getTo(): array { return $this->to; }
    public function getFrom(): ?string { return $this->from; }
    public function getAttachments(): array { return $this->attachments; }

    public function send(): self
    {
        return $this;
    }

    public function with(array|string $relations): self
    {
        return $this;
    }

    public function toMessage(): Message
    {
        $this->build();
        $msg = new Message();
        if ($this->subject !== null) $msg->subject($this->subject);
        if ($this->from !== null) $msg->from($this->from);
        foreach ($this->to as $email) $msg->to($email);
        foreach ($this->attachments as $a) $msg->attach($a['path'], $a['name'], $a['mime']);
        return $msg;
    }
}
