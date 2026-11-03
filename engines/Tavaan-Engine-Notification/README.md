# Notification

> آگاه‌سازی چندکاناله (Mail/SMS/Database) با Notification pattern.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.notification` |
| DOC-ID | `DOC-066` |
| Namespace | `Tavaan\Engine\Notifications` |
| اولویت | P1 (فاز M5) |
| Facade | `Notification` |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-Notification |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/notification.md

## استفاده

```php
use Tavaan\Engine\Notifications\NotificationManager;
use Tavaan\Engine\Notifications\Notification;
use Tavaan\Engine\Notifications\Notifiable;
use Tavaan\Engine\Notifications\SimpleNotifiable;

// تعریف Notification
class OrderShipped extends Notification {
    public function __construct(private readonly int $orderId) {}
    public function via(Notifiable $n): array { return ['mail', 'sms', 'database']; }
    public function toMail(Notifiable $n): string { return 'Order shipped!'; }
    public function toSms(Notifiable $n): string { return 'Order ' . $this->orderId . ' shipped'; }
    public function toDatabase(Notifiable $n): array { return ['order_id' => $this->orderId]; }
}

// ارسال
$m = new NotificationManager();
$user = new SimpleNotifiable(['mail' => 'u@example.com', 'sms' => '09123456789']);
$m->send($user, new OrderShipped(42));

// به چند کاربر
$m->sendTo([$user1, $user2], new OrderShipped(42));
```

## مستندات

[docs/03-engines/notification.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/notification.md)

---
*Generated on 2026-11-03.*
