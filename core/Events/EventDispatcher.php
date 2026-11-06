<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Event Dispatcher (پیاده‌سازی درگاه ۲: pub/sub)
 *
 * پیاده‌سازی سیستم رویداد. این کلاس یکی از سه درگاه ارتباط بین موتورهاست.
 *
 * امکانات:
 *   - listen() — ثبت شنونده
 *   - dispatch() — انتشار رویداد
 *   - hasListeners() — بررسی شنونده
 *   - forget() — حذف شنونده
 *
 * الگو: Observer / Event Dispatcher
 */

namespace Tavaan\Core\Events;

use Tavaan\Core\Contracts\Container\ContainerInterface;
use Tavaan\Core\Contracts\Events\DispatcherInterface;

final class EventDispatcher implements DispatcherInterface
{
    /** @var array<string, list<array{listener: callable|string|array, priority: int}>> */
    private array $listeners = [];

    public function __construct(
        private readonly ?ContainerInterface $container = null
    ) {}

    public function listen(string|array $events, callable|string|array $listener, int $priority = 0): void
    {
        foreach ((array) $events as $event) {
            $this->listeners[$event][] = ['listener' => $listener, 'priority' => $priority];
            // sort by priority descending
            usort($this->listeners[$event], fn($a, $b) => $b['priority'] <=> $a['priority']);
        }
    }

    public function dispatch(string|object $event, mixed $payload = [], bool $halt = false): mixed
    {
        [$eventName, $eventObject] = $this->resolveEvent($event, $payload);

        $listeners = $this->listeners[$eventName] ?? [];
        $responses = [];

        foreach ($listeners as $entry) {
            $response = $this->invokeListener($entry['listener'], $eventObject, $payload);

            if ($halt && $response !== null) {
                return $response;
            }

            $responses[] = $response;

            // stop propagation
            if ($eventObject instanceof \Tavaan\Core\Contracts\Events\StoppableEventInterface
                && $eventObject->isPropagationStopped()) {
                break;
            }
        }

        return $halt ? null : ($responses === [] ? null : end($responses));
    }

    public function hasListeners(string $event): bool
    {
        return !empty($this->listeners[$event]);
    }

    public function forget(string $event): void
    {
        unset($this->listeners[$event]);
    }

    /**
     * @return array{0: string, 1: object|string}
     */
    private function resolveEvent(string|object $event, mixed $payload): array
    {
        if (is_object($event)) {
            $eventName = $event::class;
            $eventObject = $event;
        } else {
            $eventName = $event;
            $eventObject = is_array($payload) ? new \stdClass() : $payload;
            if (is_array($payload)) {
                foreach ($payload as $k => $v) {
                    $eventObject->$k = $v;
                }
            }
        }
        return [$eventName, $eventObject];
    }

    private function invokeListener(callable|string|array $listener, mixed $event, mixed $payload): mixed
    {
        if (is_callable($listener)) {
            return $listener($event, $payload);
        }

        if (is_string($listener)) {
            // "Class@method" — resolve from container
            if (str_contains($listener, '@')) {
                [$class, $method] = explode('@', $listener, 2);
                $instance = $this->container?->make($class) ?? new $class();
                return $instance->$method($event, $payload);
            }
            // "Class::method" — static
            if (str_contains($listener, '::')) {
                [$class, $method] = explode('::', $listener, 2);
                return $class::$method($event, $payload);
            }
            // "Class" — __invoke
            $instance = $this->container?->make($listener) ?? new $listener();
            return $instance($event, $payload);
        }

        if (is_array($listener)) {
            [$objectOrClass, $method] = $listener;
            if (is_string($objectOrClass)) {
                $object = $this->container?->make($objectOrClass) ?? new $objectOrClass();
            } else {
                $object = $objectOrClass;
            }
            return $object->$method($event, $payload);
        }

        return null;
    }
}
