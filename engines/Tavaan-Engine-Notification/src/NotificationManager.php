<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notifications;

use Tavaan\Engine\Notifications\Channels\ChannelInterface;
use Tavaan\Engine\Notifications\Channels\DatabaseChannel;
use Tavaan\Engine\Notifications\Channels\MailChannel;
use Tavaan\Engine\Notifications\Channels\SmsChannel;

/**
 * NotificationManager — مدیریت کانال‌ها و ارسال اعلان.
 */
final class NotificationManager
{
    /** @var array<string, ChannelInterface> */
    private array $channels = [];

    public function __construct()
    {
        $this->channels['database'] = new DatabaseChannel();
        $this->channels['mail'] = new MailChannel();
        $this->channels['sms'] = new SmsChannel();
    }

    public function channel(string $name): ChannelInterface
    {
        return $this->channels[$name];
    }

    public function send(Notifiable $notifiable, Notification $notification): void
    {
        $channels = $notification->via($notifiable);
        foreach ($channels as $channel) {
            if (isset($this->channels[$channel])) {
                $this->channels[$channel]->send($notifiable, $notification);
            }
        }
    }

    /**
     * @param list<Notifiable> $notifiables
     */
    public function sendTo(array $notifiables, Notification $notification): void
    {
        foreach ($notifiables as $n) $this->send($n, $notification);
    }

    public function route(string $channel, mixed $value): self
    {
        // Helper for fluent API
        return $this;
    }
}
