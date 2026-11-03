<?php

declare(strict_types=1);

namespace Tavaan\Engine\Event;

/**
 * EventDispatcher — موتور اصلی Event Bus.
 *
 * @see docs/03-engines/event.md (DOC-033)
 */
final class EventDispatcher implements Contracts\DispatcherInterface
{
    /**
     * @var array<string, list<array{listener: callable, priority: int}>> listeners indexed by exact event name
     */
    private array $listeners = [];

    private WildcardMatcher $wildcards;

    private bool $faked = false;

    /**
     * @var list<array{event: string, payload: array<string, mixed>}>
     */
    private array $dispatched = [];

    /**
     * @var list<string> events to fake (empty = all)
     */
    private array $fakeWhitelist = [];

    public function __construct(
        private readonly ?object $container = null,
    ) {
        $this->wildcards = new WildcardMatcher();
    }

    public function listen(string|\Closure $event, \Closure|string|array|null $listener = null, int $priority = 0): void
    {
        // If $event is a Closure, it's the listener for all events
        if ($event instanceof \Closure) {
            $this->listen('*', $event, $priority);
            return;
        }
        $listener ??= fn() => null;
        $callable = $this->makeCallable($listener);
        if (\str_contains($event, '*')) {
            $this->wildcards->add($event, $callable);
            return;
        }
        $this->listeners[$event][] = ['listener' => $callable, 'priority' => $priority];
        // Sort by priority descending (higher first)
        \usort($this->listeners[$event], fn($a, $b) => $b['priority'] <=> $a['priority']);
    }

    public function subscribe(string $subscriber): void
    {
        if ($this->container !== null && \method_exists($this->container, 'make')) {
            $instance = $this->container->make($subscriber);
        } else {
            $instance = new $subscriber();
        }
        if (\method_exists($instance, 'subscribe')) {
            $instance->subscribe($this);
        }
    }

    public function dispatch(object|string $event, array $payload = [], bool $halt = false): mixed
    {
        $eventName = \is_object($event) ? \get_class($event) : $event;
        $args = \is_object($event) ? [$event] : $payload;

        if ($this->faked && $this->shouldFake($eventName)) {
            $this->dispatched[] = ['event' => $eventName, 'payload' => $args];
            return null;
        }

        $listeners = $this->getListeners($eventName);
        $haltResult = null;
        foreach ($listeners as $listener) {
            $result = $listener(...$args);
            if ($halt && $result !== null) {
                $haltResult = $result;
                break;
            }
        }
        return $haltResult;
    }

    public function until(object|string $event, array $payload = []): mixed
    {
        return $this->dispatch($event, $payload, halt: true);
    }

    public function hasListeners(string $event): bool
    {
        return !empty($this->listeners[$event]) || !empty($this->wildcards->match($event));
    }

    public function getListeners(string $event): array
    {
        $exact = \array_map(fn($item) => $item['listener'], $this->listeners[$event] ?? []);
        $wildcards = $this->wildcards->match($event);
        return \array_merge($exact, $wildcards);
    }

    public function forget(string $event): void
    {
        unset($this->listeners[$event]);
        if (\str_contains($event, '*')) {
            $this->wildcards->remove($event);
        }
    }

    public function fake(array|string $events = []): void
    {
        $this->faked = true;
        $this->fakeWhitelist = (array) $events;
        $this->dispatched = [];
    }

    public function assertDispatched(string $event, ?callable $callback = null): void
    {
        $count = 0;
        foreach ($this->dispatched as $d) {
            if ($d['event'] === $event) {
                if ($callback === null || $callback($d['payload'])) {
                    $count++;
                }
            }
        }
        if ($count === 0) {
            throw new \RuntimeException("Expected event '{$event}' to be dispatched, but it was not.");
        }
    }

    public function assertNotDispatched(string $event): void
    {
        $count = \count(\array_filter($this->dispatched, fn($d) => $d['event'] === $event));
        if ($count > 0) {
            throw new \RuntimeException("Expected event '{$event}' NOT to be dispatched, but it was ({$count} times).");
        }
    }

    public function assertDispatchedTimes(string $event, int $times = 1): void
    {
        $count = \count(\array_filter($this->dispatched, fn($d) => $d['event'] === $event));
        if ($count !== $times) {
            throw new \RuntimeException("Expected event '{$event}' to be dispatched {$times} times, but it was {$count}.");
        }
    }

    /**
     * @return list<array{event: string, payload: array<string, mixed>}>
     */
    public function dispatchedEvents(): array
    {
        return $this->dispatched;
    }

    public function isFaked(): bool
    {
        return $this->faked;
    }

    public function clearFake(): void
    {
        $this->faked = false;
        $this->fakeWhitelist = [];
        $this->dispatched = [];
    }

    private function shouldFake(string $event): bool
    {
        if ($this->fakeWhitelist === []) {
            return true;  // fake all
        }
        return \in_array($event, $this->fakeWhitelist, true);
    }

    private function makeCallable(\Closure|string|array $listener): callable
    {
        if ($listener instanceof \Closure) {
            return $listener;
        }
        if (\is_string($listener)) {
            if (\str_contains($listener, '@')) {
                [$class, $method] = \explode('@', $listener, 2);
                return function (...$args) use ($class, $method) {
                    $instance = $this->resolve($class);
                    return $instance->{$method}(...$args);
                };
            }
            // Just a class — call handle()
            return function (...$args) use ($listener) {
                $instance = $this->resolve($listener);
                if (\method_exists($instance, 'handle')) {
                    return $instance->handle(...$args);
                }
                return null;
            };
        }
        if (\is_array($listener)) {
            [$class, $method] = $listener;
            return function (...$args) use ($class, $method) {
                $instance = \is_object($class) ? $class : $this->resolve($class);
                return $instance->{$method}(...$args);
            };
        }
        return fn() => null;
    }

    private function resolve(string $class): object
    {
        if ($this->container !== null && \method_exists($this->container, 'make')) {
            return $this->container->make($class);
        }
        return new $class();
    }

    /**
     * extend
     */
    public function extend(string $event, \Closure $extender): void
    {
        $this->extenders[$event][] = $extender;
    }
    /**
     * push
     */
    public function push(): self
    {
        return $this->save();
    }
}
