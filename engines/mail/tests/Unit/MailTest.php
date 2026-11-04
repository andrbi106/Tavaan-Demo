<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Mail\Message;
use Tavaan\Engine\Mail\SentMessage;
use Tavaan\Engine\Mail\Mailable;
use Tavaan\Engine\Mail\MailManager;
use Tavaan\Engine\Mail\Transports\ArrayTransport;
use Tavaan\Engine\Mail\Transports\LogTransport;
use Tavaan\Engine\Mail\Contracts\TransportInterface;

final class MailTest extends TestCase
{
    public function test_message_fluent_api(): void
    {
        $msg = (new Message())
            ->to('user@example.com')
            ->cc('admin@example.com')
            ->bcc('audit@example.com')
            ->from('noreply@example.com', 'Tavaan')
            ->replyTo('support@example.com')
            ->subject('Welcome')
            ->html('<p>Hello</p>')
            ->text('Hello');

        $this->assertSame(['user@example.com'], $msg->getTo());
        $this->assertSame(['admin@example.com'], $msg->getCc());
        $this->assertSame(['audit@example.com'], $msg->getBcc());
        $this->assertSame('noreply@example.com', $msg->getFrom());
        $this->assertSame('Tavaan', $msg->getFromName());
        $this->assertSame(['support@example.com'], $msg->getReplyTo());
        $this->assertSame('Welcome', $msg->getSubject());
        $this->assertSame('<p>Hello</p>', $msg->getHtmlBody());
        $this->assertSame('Hello', $msg->getTextBody());
    }

    public function test_message_attach(): void
    {
        $msg = (new Message())->attach('/path/file.pdf', 'doc.pdf', 'application/pdf');
        $attachments = $msg->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertSame('/path/file.pdf', $attachments[0]['path']);
        $this->assertSame('doc.pdf', $attachments[0]['name']);
        $this->assertSame('application/pdf', $attachments[0]['mime']);
    }

    public function test_array_transport_stores_messages(): void
    {
        $t = new ArrayTransport();
        $msg = (new Message())->to('u@example.com')->subject('Test');
        $sent = $t->send($msg);
        $this->assertInstanceOf(SentMessage::class, $sent);
        $this->assertCount(1, $t->messages());
        $this->assertSame($msg, $t->messages()[0]);
    }

    public function test_array_transport_count(): void
    {
        $t = new ArrayTransport();
        $this->assertSame(0, $t->count());
        $t->send((new Message())->to('u@example.com'));
        $t->send((new Message())->to('u2@example.com'));
        $this->assertSame(2, $t->count());
    }

    public function test_array_transport_reset(): void
    {
        $t = new ArrayTransport();
        $t->send((new Message())->to('u@example.com'));
        $t->reset();
        $this->assertSame(0, $t->count());
    }

    public function test_log_transport_writes_to_log(): void
    {
        $logFile = \tempnam(\sys_get_temp_dir(), 'tvn_mail') . '.log';
        $t = new LogTransport($logFile);
        $msg = (new Message())->to('user@example.com')->subject('Test');
        $t->send($msg);
        $content = \file_get_contents($logFile);
        $this->assertStringContainsString('user@example.com', $content);
        $this->assertStringContainsString('Test', $content);
        @\unlink($logFile);
    }

    public function test_mailable_build_pattern(): void
    {
        $mailable = new class extends Mailable {
            public function __construct() {}
            public function build(): self
            {
                return $this->subject('Welcome')->view('emails.welcome', ['name' => 'Ali'])->to('u@example.com')->from('noreply@example.com');
            }
        };
        $message = $mailable->toMessage();
        $this->assertSame('Welcome', $message->getSubject());
        $this->assertSame(['u@example.com'], $message->getTo());
        $this->assertSame('noreply@example.com', $message->getFrom());
    }

    public function test_mailable_attach(): void
    {
        $mailable = new class extends Mailable {
            public function build(): self
            {
                return $this->subject('T')->attach('/path/invoice.pdf', as: 'invoice.pdf');
            }
        };
        $message = $mailable->toMessage();
        $this->assertCount(1, $message->getAttachments());
    }

    public function test_mail_manager_send_with_log_driver(): void
    {
        $logFile = \tempnam(\sys_get_temp_dir(), 'tvn_mail') . '.log';
        $m = new MailManager(['driver' => 'log', 'log_path' => $logFile]);
        $msg = (new Message())->to('u@example.com')->subject('T');
        $m->send($msg);
        $this->assertFileExists($logFile);
        @\unlink($logFile);
    }

    public function test_mail_manager_send_with_array_driver(): void
    {
        $m = new MailManager(['driver' => 'array']);
        $msg = (new Message())->to('u@example.com')->subject('T');
        $sent = $m->send($msg);
        $this->assertInstanceOf(SentMessage::class, $sent);
    }

    public function test_mail_manager_fake_mode(): void
    {
        $m = new MailManager([]);
        $m->fake();
        $this->assertTrue($m->isFaked());
        $m->send((new Message())->to('u@example.com'));
        $this->assertCount(1, $m->sentMessages());
    }

    public function test_mail_manager_to_chaining(): void
    {
        $logFile = \tempnam(\sys_get_temp_dir(), 'tvn_mail') . '.log';
        $m = new MailManager(['driver' => 'log', 'log_path' => $logFile]);
        $mailable = new class extends Mailable {
            public function build(): self
            {
                return $this->subject('Welcome')->to('default@example.com');
            }
        };
        $m->to('override@example.com')->send($mailable);
        $content = \file_get_contents($logFile);
        $this->assertStringContainsString('override@example.com', $content);
        @\unlink($logFile);
    }

    public function test_transport_interface_implemented(): void
    {
        $this->assertInstanceOf(TransportInterface::class, new ArrayTransport());
        $this->assertInstanceOf(TransportInterface::class, new LogTransport('/tmp/test.log'));
    }
}
