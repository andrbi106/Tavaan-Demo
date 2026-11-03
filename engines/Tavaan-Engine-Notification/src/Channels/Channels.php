<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notifications\Channels;

use Tavaan\Engine\Notifications\Notifiable;
use Tavaan\Engine\Notifications\Notification;

/**
 * قرارداد Channel.
 */
interface ChannelInterface
{
    public function send(Notifiable $notifiable, Notification $notification): mixed;
}

/**
 * DatabaseChannel — ذخیره در حافظه (در عمل در جدول notifications).
 */
final class DatabaseChannel implements ChannelInterface
{
    /** @var list<array{notifiable: Notifiable, data: mixed}> */
    private array $stored = [];

    public function send(Notifiable $n, Notification $notification): mixed
    {
        $data = $notification->to('database', $n);
        $this->stored[] = ['notifiable' => $n, 'data' => $data];
        return $data;
    }

    public function stored(): array
    {
        return $this->stored;
    }

    public function clear(): void
    {
        $this->stored = [];
    }
}

/**
 * MailChannel — ساخت Message برای ارسال (در عمل با موتور mail).
 */
final class MailChannel implements ChannelInterface
{
    /** @var list<array{to: mixed, message: mixed}> */
    private array $sent = [];

    public function send(Notifiable $n, Notification $notification): mixed
    {
        $to = $n->routeNotificationFor('mail');
        $message = $notification->to('mail', $n);
        $this->sent[] = ['to' => $to, 'message' => $message];
        return $message;
    }

    public function sent(): array { return $this->sent; }
    public function clear(): void { $this->sent = []; }
}

/**
 * SmsChannel — برای SMS.
 */
final class SmsChannel implements ChannelInterface
{
    /** @var list<array{to: mixed, message: mixed}> */
    private array $sent = [];

    public function send(Notifiable $n, Notification $notification): mixed
    {
        $to = $n->routeNotificationFor('sms');
        $message = $notification->to('sms', $n);
        $this->sent[] = ['to' => $to, 'message' => $message];
        return $message;
    }

    public function sent(): array { return $this->sent; }
    public function clear(): void { $this->sent = []; }
}
