<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Mail\MailManager;
use Tavaan\Engine\Mail\MailServiceProvider;
use Tavaan\Engine\Mail\Contracts\TransportInterface;

final class MailFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new MailServiceProvider(['driver' => 'array']);
        $provider->register();
        $this->assertInstanceOf(MailManager::class, $provider->getManager());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new MailServiceProvider([]);
        $this->assertContains(MailManager::class, $provider->provides());
        $this->assertContains(TransportInterface::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/mail.php';
        $this->assertArrayHasKey('driver', $config);
        $this->assertArrayHasKey('from', $config);
        $this->assertArrayHasKey('smtp', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/mail.php';
        $this->assertArrayHasKey('error_send', $messages);
    }

    public function test_end_to_end_send_via_fake(): void
    {
        $provider = new MailServiceProvider(['driver' => 'array']);
        $provider->register();
        $m = $provider->getManager();
        $m->fake();
        $m->send((new \Tavaan\Engine\Mail\Message())->to('u@example.com')->subject('T'));
        $this->assertCount(1, $m->sentMessages());
    }
}
