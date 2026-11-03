<?php

declare(strict_types=1);

namespace Tavaan\Engine\Workflow;

/**
 * ProcessInstance — runtime state of a workflow instance.
 * Wraps a WorkflowInstance and exposes a richer API.
 */
final class ProcessInstance
{
    private bool $cancelled = false;
    private bool $completed = false;

    /**
     * @param array<string, mixed> $variables
     */
    public function __construct(
        public readonly WorkflowInstance $instance,
        private array $variables = [],
    ) {}

    /**
     * fire — execute a transition by action name.
     */
    public function fire(string $action): bool
    {
        if ($this->cancelled || $this->completed) { return false; }
        $ok = $this->instance->transition($action);
        return $ok;
    }

    /**
     * currentState — return current state name.
     */
    public function currentState(): string
    {
        return $this->instance->getState();
    }

    public function can(string $permission): bool
    {
        return $this->instance->can($permission);
    }

    /**
     * variables — return all process variables.
     * @return array<string, mixed>
     */
    public function variables(): array
    {
        return array_merge($this->instance->getContext(), $this->variables);
    }

    /**
     * setVariable — update a process variable.
     */
    public function setVariable(string $key, mixed $value): void
    {
        $this->variables[$key] = $value;
        $this->instance->setContext($key, $value);
    }

    /**
     * complete — mark the instance as complete.
     */
    public function complete(): bool
    {
        if ($this->completed || $this->cancelled) { return false; }
        $this->completed = true;
        return true;
    }

    /**
     * cancel — mark the instance as cancelled.
     */
    public function cancel(): bool
    {
        if ($this->cancelled || $this->completed) { return false; }
        $this->cancelled = true;
        return true;
    }

    public function isCompleted(): bool { return $this->completed; }
    public function isCancelled(): bool { return $this->cancelled; }
    public function availableActions(): array { return $this->instance->availableActions(); }
}
