<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notification\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Notification\NotificationManager;
use Tavaan\Engine\Notification\Notification;
use Tavaan\Engine\Notification\Contracts\Notifiable;
use Tavaan\Engine\Notification\SimpleNotifiable;
use Tavaan\Engine\Notification\Channels\DatabaseChannel;
use Tavaan\Engine\Notification\Channels\MailChannel;
use Tavaan\Engine\Notification\Channels\SmsChannel;
use Tavaan\Engine\Notification\Channels\ChannelInterface;

final class NotificationTest extends TestCase
{
    public function test_database_channel_stores(): void
    {
        $ch = new DatabaseChannel();
        $n = new SimpleNotifiable([]);
        $notif = new class extends Notification {
            public function via(Notifiable $n): array { return ['database']; }
            public function toDatabase(Notifiable $n): array { return ['msg' => 'hello']; }
        };
        $ch->send($n, $notif);
        $this->assertCount(1, $ch->stored());
        $this->assertSame(['msg' => 'hello'], $ch->stored()[0]['data']);
    }

    public function test_mail_channel_uses_route(): void
    {
        $ch = new MailChannel();
        $n = new SimpleNotifiable(['mail' => 'user@example.com']);
        $notif = new class extends Notification {
            public function via(Notifiable $n): array { return ['mail']; }
            public function toMail(Notifiable $n): string { return 'email content'; }
        };
        $ch->send($n, $notif);
        $this->assertSame('user@example.com', $ch->sent()[0]['to']);
        $this->assertSame('email content', $ch->sent()[0]['message']);
    }

    public function test_sms_channel(): void
    {
        $ch = new SmsChannel();
        $n = new SimpleNotifiable(['sms' => '09123456789']);
        $notif = new class extends Notification {
            public function via(Notifiable $n): array { return ['sms']; }
            public function toSms(Notifiable $n): string { return 'SMS body'; }
        };
        $ch->send($n, $notif);
        $this->assertSame('09123456789', $ch->sent()[0]['to']);
    }

    public function test_manager_sends_to_multiple_channels(): void
    {
        $m = new NotificationManager();
        $n = new SimpleNotifiable(['mail' => 'u@e.com', 'sms' => '0912']);
        $notif = new class extends Notification {
            public function via(Notifiable $n): array { return ['mail', 'sms', 'database']; }
            public function toMail(Notifiable $n): string { return 'mail'; }
            public function toSms(Notifiable $n): string { return 'sms'; }
            public function toDatabase(Notifiable $n): array { return ['x']; }
        };
        $m->send($n, $notif);
        /** @var MailChannel $mail */
        $mail = $m->channel('mail');
        $this->assertCount(1, $mail->sent());
        /** @var SmsChannel $sms */
        $sms = $m->channel('sms');
        $this->assertCount(1, $sms->sent());
        /** @var DatabaseChannel $db */
        $db = $m->channel('database');
        $this->assertCount(1, $db->stored());
    }

    public function test_manager_send_to_multiple_notifiables(): void
    {
        $m = new NotificationManager();
        $n1 = new SimpleNotifiable(['mail' => 'a@e.com']);
        $n2 = new SimpleNotifiable(['mail' => 'b@e.com']);
        $notif = new class extends Notification {
            public function via(Notifiable $n): array { return ['mail']; }
            public function toMail(Notifiable $n): string { return 'msg'; }
        };
        $m->sendTo([$n1, $n2], $notif);
        $mail = $m->channel('mail');
        $this->assertCount(2, $mail->sent());
    }

    public function test_channels_clear(): void
    {
        $ch = new DatabaseChannel();
        $ch->send(new SimpleNotifiable([]), new class extends Notification {
            public function via(Notifiable $n): array { return ['database']; }
            public function toDatabase(Notifiable $n): array { return []; }
        });
        $ch->clear();
        $this->assertSame([], $ch->stored());
    }

    public function test_channel_interface_implemented(): void
    {
        $this->assertInstanceOf(ChannelInterface::class, new DatabaseChannel());
        $this->assertInstanceOf(ChannelInterface::class, new MailChannel());
        $this->assertInstanceOf(ChannelInterface::class, new SmsChannel());
    }
}
