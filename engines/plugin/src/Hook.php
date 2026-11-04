<?php

declare(strict_types=1);

namespace Tavaan\Engine\Plugin;

/**
 * Hook — action/filter hook system (WordPress-style).
 */
final class Hook
{
    /** @var array<string, list<callable>> */
    private array $listeners = [];

    /**
     * listen — register a callback for a hook name with priority.
     */
    public function listen(string $name, callable $callback, int $priority = 10): void
    {
        $this->listeners[$name][] = ['cb' => $callback, 'priority' => $priority];
        usort($this->listeners[$name], fn($a, $b) => $a['priority'] <=> $b['priority']);
    }

    /**
     * fire — invoke all listeners for a hook with a value chain.
     */
    public function fire(string $name, mixed $value = null, array $args = []): mixed
    {
        foreach ($this->listeners[$name] ?? [] as $entry) {
            $value = ($entry['cb'])($value, ...$args);
        }
        return $value;
    }

    /**
     * has — are there any listeners for this hook?
     */
    public function has(string $name): bool
    {
        return !empty($this->listeners[$name]);
    }

    /**
     * remove — drop a specific listener (or all for a name).
     */
    public function remove(string $name, ?callable $callback = null): void
    {
        if ($callback === null) {
            unset($this->listeners[$name]);
            return;
        }
        $this->listeners[$name] = array_values(array_filter(
            $this->listeners[$name] ?? [],
            fn($e) => $e['cb'] !== $callback
        ));
    }
}
