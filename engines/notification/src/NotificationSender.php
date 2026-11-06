<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notification;

final class NotificationSender
{
    private array $sent = [];

    public function __construct(private readonly array $config = []) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        $channels = $notification->via($notifiable);
        foreach ($channels as $channel) {
            $this->sent[] = ['notifiable' => $notifiable, 'notification' => $notification, 'channel' => $channel];
        }
    }

    public function sendNow(mixed $notifiable, Notification $notification): void
    {
        $this->send($notifiable, $notification);
    }

    public function route(string $channel, mixed $value): self
    {
        return $this;
    }

    public function getSent(): array
    {
        return $this->sent;
    }
}
