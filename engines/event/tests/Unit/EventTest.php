<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Event\EventDispatcher;
use Tavaan\Engine\Event\WildcardMatcher;
use Tavaan\Engine\Event\Contracts\DispatcherInterface;

/**
 * تست‌های Unit برای موتور Event.
 *
 * @see docs/03-engines/event.md (DOC-033)
 */
final class EventTest extends TestCase
{
    public function test_listen_and_dispatch_with_closure(): void
    {
        $d = new EventDispatcher();
        $received = [];
        $d->listen('user.created', function ($event) use (&$received) {
            $received[] = $event;
        });
        $d->dispatch('user.created', ['Alice']);
        $this->assertCount(1, $received);
        $this->assertSame(['Alice'], $received[0]);
    }

    public function test_dispatch_with_event_object(): void
    {
        $d = new EventDispatcher();
        $received = [];
        $d->listen(TestEvent::class, function (TestEvent $event) use (&$received) {
            $received[] = $event->name;
        });
        $d->dispatch(new TestEvent('hello'));
        $this->assertCount(1, $received);
        $this->assertSame('hello', $received[0]);
    }

    public function test_priority_order(): void
    {
        $d = new EventDispatcher();
        $order = [];
        $d->listen('e', function () use (&$order) { $order[] = 'low'; }, priority: 0);
        $d->listen('e', function () use (&$order) { $order[] = 'high'; }, priority: 100);
        $d->dispatch('e');
        $this->assertSame(['high', 'low'], $order);
    }

    public function test_wildcard_listen(): void
    {
        $d = new EventDispatcher();
        $received = [];
        $d->listen('shop.order.*', function (...$args) use (&$received) {
            $received[] = $args;
        });
        $d->dispatch('shop.order.paid', ['order_id' => 1]);
        $d->dispatch('shop.order.cancelled', ['order_id' => 2]);
        $this->assertCount(2, $received);
    }

    public function test_wildcard_at_middle(): void
    {
        $d = new EventDispatcher();
        $received = 0;
        $d->listen('shop.*.paid', function () use (&$received) { $received++; });
        $d->dispatch('shop.order.paid');
        $this->assertSame(1, $received);
    }

    public function test_has_listeners(): void
    {
        $d = new EventDispatcher();
        $d->listen('e1', fn() => null);
        $this->assertTrue($d->hasListeners('e1'));
        $this->assertFalse($d->hasListeners('e2'));
    }

    public function test_get_listeners(): void
    {
        $d = new EventDispatcher();
        $d->listen('e', fn() => null);
        $d->listen('e', fn() => null);
        $this->assertCount(2, $d->getListeners('e'));
    }

    public function test_forget(): void
    {
        $d = new EventDispatcher();
        $d->listen('e', fn() => null);
        $d->forget('e');
        $this->assertFalse($d->hasListeners('e'));
    }

    public function test_until_stops_on_non_null(): void
    {
        $d = new EventDispatcher();
        $d->listen('e', fn() => null);
        $d->listen('e', fn() => 'stop');
        $d->listen('e', fn() => 'after');
        $result = $d->until('e');
        $this->assertSame('stop', $result);
    }

    public function test_until_returns_null_when_all_return_null(): void
    {
        $d = new EventDispatcher();
        $d->listen('e', fn() => null);
        $d->listen('e', fn() => null);
        $this->assertNull($d->until('e'));
    }

    public function test_dispatch_with_halt(): void
    {
        $d = new EventDispatcher();
        $order = [];
        $d->listen('e', function () use (&$order) { $order[] = 1; return null; });
        $d->listen('e', function () use (&$order) { $order[] = 2; return 'stop'; });
        $d->listen('e', function () use (&$order) { $order[] = 3; return null; });
        $result = $d->dispatch('e', [], halt: true);
        $this->assertSame('stop', $result);
        $this->assertSame([1, 2], $order);
    }

    public function test_subscribe_with_class(): void
    {
        $d = new EventDispatcher();
        $d->subscribe(TestSubscriber::class);
        $this->assertTrue($d->hasListeners('sub.event1'));
        $this->assertTrue($d->hasListeners('sub.event2'));
    }

    public function test_class_string_listener(): void
    {
        $d = new EventDispatcher();
        $d->listen('e', TestListener::class);
        $received = null;
        $d->dispatch('e', []);
        // TestListener sets global state — just verify no exception
        $this->assertTrue(true);
    }

    public function test_class_at_method_listener(): void
    {
        $d = new EventDispatcher();
        $d->listen('e', TestListener::class . '@handle');
        $d->dispatch('e', []);
        $this->assertTrue(true);
    }

    public function test_array_listener(): void
    {
        $d = new EventDispatcher();
        $listener = new TestListener();
        $d->listen('e', [$listener, 'handle']);
        $d->dispatch('e', []);
        $this->assertSame(1, $listener->called);
    }

    public function test_fake_mode(): void
    {
        $d = new EventDispatcher();
        $d->fake();
        $received = [];
        $d->listen('e', function () use (&$received) { $received[] = 1; });
        $d->dispatch('e');
        $this->assertSame([], $received);
        $this->assertTrue($d->isFaked());
    }

    public function test_fake_with_whitelist(): void
    {
        $d = new EventDispatcher();
        $d->fake(['fake.event']);
        $actual_received = [];
        $d->listen('real.event', function () use (&$actual_received) { $actual_received[] = 1; });
        $d->listen('fake.event', function () { /* not called */ });
        $d->dispatch('real.event');  // should be real
        $d->dispatch('fake.event');  // should be faked
        $this->assertCount(1, $actual_received);
    }

    public function test_assert_dispatched(): void
    {
        $d = new EventDispatcher();
        $d->fake();
        $d->dispatch('test.event', ['x' => 1]);
        $d->assertDispatched('test.event');
        $d->assertDispatched('test.event', fn($p) => $p['x'] === 1);
    }

    public function test_assert_not_dispatched(): void
    {
        $d = new EventDispatcher();
        $d->fake();
        $d->assertNotDispatched('never.dispatched');
    }

    public function test_assert_dispatched_times(): void
    {
        $d = new EventDispatcher();
        $d->fake();
        $d->dispatch('e');
        $d->dispatch('e');
        $d->dispatch('e');
        $d->assertDispatchedTimes('e', 3);
    }

    public function test_assert_dispatched_throws_when_not_dispatched(): void
    {
        $d = new EventDispatcher();
        $d->fake();
        $this->expectException(\RuntimeException::class);
        $d->assertDispatched('never.dispatched');
    }

    public function test_assert_dispatched_times_throws_when_wrong_count(): void
    {
        $d = new EventDispatcher();
        $d->fake();
        $d->dispatch('e');
        $this->expectException(\RuntimeException::class);
        $d->assertDispatchedTimes('e', 5);
    }

    public function test_clear_fake(): void
    {
        $d = new EventDispatcher();
        $d->fake();
        $d->dispatch('e');
        $d->clearFake();
        $this->assertFalse($d->isFaked());
        $this->assertSame([], $d->dispatchedEvents());
    }

    public function test_dispatcher_implements_interface(): void
    {
        $d = new EventDispatcher();
        $this->assertInstanceOf(DispatcherInterface::class, $d);
    }

    public function test_wildcard_matcher_basic(): void
    {
        $m = new WildcardMatcher();
        $m->add('shop.order.*', fn() => null);
        $this->assertCount(1, $m->match('shop.order.paid'));
        $this->assertCount(1, $m->match('shop.order.cancelled'));
        $this->assertCount(0, $m->match('shop.user.created'));
    }

    public function test_wildcard_matcher_exact(): void
    {
        $m = new WildcardMatcher();
        $m->add('exact.event', fn() => null);
        $this->assertCount(1, $m->match('exact.event'));
        $this->assertCount(0, $m->match('other.event'));
    }

    public function test_wildcard_matcher_clear(): void
    {
        $m = new WildcardMatcher();
        $m->add('e.*', fn() => null);
        $m->clear();
        $this->assertCount(0, $m->match('e.test'));
    }

    public function test_listen_with_closure_as_event(): void
    {
        $d = new EventDispatcher();
        $received = [];
        $d->listen(function ($event) use (&$received) { $received[] = $event; });
        // This should listen for '*' (all events)
        $d->dispatch('any.event');
        $this->assertTrue(true);  // just verifying no exception
    }
}

// === Helper classes ===

final class TestEvent
{
    public function __construct(public string $name)
    {
    }
}

final class TestListener
{
    public int $called = 0;

    public function handle(...$args): void
    {
        $this->called++;
    }
}

final class TestSubscriber
{
    public function subscribe(EventDispatcher $dispatcher): void
    {
        $dispatcher->listen('sub.event1', fn() => null);
        $dispatcher->listen('sub.event2', fn() => null);
    }
}
