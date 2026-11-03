<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notifications;

/**
 * Notifiable — کلاس‌هایی که notification دریافت می‌کنند.
 *
 * @see docs/03-engines/notification.md
 */
interface Notifiable
{
    /**
     * @return array<string, mixed>
     */
    public function routeNotificationFor(string $channel): mixed;
}

/**
 * Notification — کلاس پایه برای اعلان‌ها.
 */
abstract class Notification
{
    /**
     * @return list<string>
     */
    abstract public function via(Notifiable $notifiable): array;

    /**
     * @param string $channel
     * @return mixed
     */
    public function to(string $channel, Notifiable $n): mixed
    {
        $method = 'to' . \ucfirst($channel);
        if (\method_exists($this, $method)) {
            return $this->{$method}($n);
        }
        return null;
    }
}

/**
 * SimpleNotifiable — implement ساده Notifiable با routes.
 */
final class SimpleNotifiable implements Notifiable
{
    /**
     * @param array<string, mixed> $routes
     */
    public function __construct(
        public readonly array $routes = [],
    ) {
    }

    public function routeNotificationFor(string $channel): mixed
    {
        return $this->routes[$channel] ?? null;
    }

    public function toDatabase(Notifiable $n): array { return []; }
    public function toMail(Notifiable $n): mixed
    {
        $email = $n->routeNotificationFor('mail');
        if ($email === null) { return null; }
        return [
            'channel' => 'mail',
            'to' => $email,
            'subject' => 'Notification',
            'body' => 'You have a new notification.',
            'headers' => [],
        ];
    }

    public function toSms(Notifiable $n): mixed
    {
        $phone = $n->routeNotificationFor('sms');
        if ($phone === null) { return null; }
        return [
            'channel' => 'sms',
            'to' => $phone,
            'message' => 'You have a new notification.',
        ];
    }

    public function toTelegram(Notifiable $n): mixed
    {
        $chatId = $n->routeNotificationFor('telegram');
        if ($chatId === null) { return null; }
        return [
            'channel' => 'telegram',
            'chat_id' => $chatId,
            'text' => 'You have a new notification.',
            'parse_mode' => 'HTML',
        ];
    }
}
