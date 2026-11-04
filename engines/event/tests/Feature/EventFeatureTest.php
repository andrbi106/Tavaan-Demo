<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Event\EventDispatcher;
use Tavaan\Engine\Event\EventServiceProvider;
use Tavaan\Engine\Event\Contracts\DispatcherInterface;

/**
 * تست‌های Feature برای موتور Event.
 */
final class EventFeatureTest extends TestCase
{
    public function test_service_provider_creates_dispatcher(): void
    {
        $provider = new EventServiceProvider();
        $provider->register();
        $d = $provider->getDispatcher();
        $this->assertInstanceOf(EventDispatcher::class, $d);
        $this->assertInstanceOf(DispatcherInterface::class, $d);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new EventServiceProvider();
        $this->assertContains(DispatcherInterface::class, $provider->provides());
        $this->assertContains(EventDispatcher::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/event.php';
        $this->assertArrayHasKey('fake', $config);
        $this->assertArrayHasKey('wildcard_default', $config);
        $this->assertArrayHasKey('discovery', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/event.php';
        $this->assertArrayHasKey('error_no_listener', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/event.php';
        $this->assertArrayHasKey('error_no_listener', $messages);
    }

    public function test_end_to_end_event_dispatch(): void
    {
        $provider = new EventServiceProvider();
        $provider->register();
        $d = $provider->getDispatcher();

        $events = [];
        $d->listen('user.created', function ($name) use (&$events) {
            $events[] = "user:{$name}";
        });
        $d->listen('user.*', function ($payload) use (&$events) {
            $events[] = 'wildcard';
        });

        $d->dispatch('user.created', ['Alice']);
        $this->assertContains('user:Alice', $events);
        $this->assertContains('wildcard', $events);
    }

    public function test_event_fake_for_testing(): void
    {
        $provider = new EventServiceProvider();
        $provider->register();
        $d = $provider->getDispatcher();
        $d->fake();

        $realListeners = [];
        $d->listen('test.event', function () use (&$realListeners) { $realListeners[] = 1; });
        $d->dispatch('test.event');

        $this->assertSame([], $realListeners);
        $d->assertDispatched('test.event');
    }
}
