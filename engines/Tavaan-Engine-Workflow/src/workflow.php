<?php
declare(strict_types=1);
namespace Tavaan\Engine\Workflow;

final class WorkflowState
{
    public function __construct(
        public readonly string $name,
        public readonly array $actions = [],
        public readonly array $metadata = [],
    ) {}
}

final class WorkflowTransition
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly string $action,
        public readonly mixed $guard = null,
    ) {}
}

final class WorkflowDefinition
{
    private array $states = [];
    private array $transitions = [];
    private string $initialState;

    public function __construct(public readonly string $name) {}

    public function addState(string $name, array $actions = [], array $metadata = []): self { $this->states[$name] = new WorkflowState($name, $actions, $metadata); return $this; }
    public function setInitial(string $state): self { $this->initialState = $state; return $this; }
    public function addTransition(string $from, string $to, string $action, ?callable $guard = null): self { $this->transitions[] = new WorkflowTransition($from, $to, $action, $guard); return $this; }
    public function getStates(): array { return $this->states; }
    public function getTransitions(): array { return $this->transitions; }
    public function getInitialState(): string { return $this->initialState; }
    public function getState(string $name): ?WorkflowState { return $this->states[$name] ?? null; }
    public function getTransitionsFrom(string $state): array { return array_filter($this->transitions, fn($t) => $t->from === $state); }
    public function canTransition(string $from, string $to, string $action): bool
    {
        foreach ($this->transitions as $t) { if ($t->from === $from && $t->to === $to && $t->action === $action) { if ($t->guard !== null && !($t->guard)()) return false; return true; } }
        return false;
    }
}

final class WorkflowInstance
{
    public function __construct(
        public readonly string $id,
        public readonly WorkflowDefinition $definition,
        private string $currentState,
        private array $context = [],
    ) {}

    public function getState(): string { return $this->currentState; }
    public function getContext(): array { return $this->context; }
    public function setContext(string $key, mixed $value): void { $this->context[$key] = $value; }
    public function transition(string $action): bool
    {
        foreach ($this->definition->getTransitions() as $t) {
            if ($t->from === $this->currentState && $t->action === $action) {
                if ($t->guard !== null && !($t->guard)()) return false;
                $this->currentState = $t->to;
                return true;
            }
        }
        return false;
    }
    public function can(string $action): bool
    {
        foreach ($this->definition->getTransitions() as $t) { if ($t->from === $this->currentState && $t->action === $action) { if ($t->guard !== null && !($t->guard)()) return false; return true; } }
        return false;
    }
    public function availableActions(): array
    {
        $actions = [];
        foreach ($this->definition->getTransitions() as $t) { if ($t->from === $this->currentState) { $actions[] = $t->action; } }
        return array_unique($actions);
    }
}

final class WorkflowManager
{
    private array $definitions = [];

    public function __construct(private readonly array $config = []) {}

    public function register(WorkflowDefinition $definition): self { $this->definitions[$definition->name] = $definition; return $this; }
    public function get(string $name): ?WorkflowDefinition { return $this->definitions[$name] ?? null; }
    public function has(string $name): bool { return isset($this->definitions[$name]); }
    public function all(): array { return array_keys($this->definitions); }

    public function start(string $name, array $context = []): ?WorkflowInstance
    {
        $def = $this->get($name);
        if ($def === null) return null;
        $id = bin2hex(random_bytes(16));
        return new WorkflowInstance($id, $def, $def->getInitialState(), $context);
    }
}

final class WorkflowServiceProvider
{
    private ?WorkflowManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new WorkflowManager($this->config); }
    public function boot(): void {}
    public function provides(): array { return [WorkflowManager::class, WorkflowDefinition::class, WorkflowInstance::class]; }
    public function getManager(): WorkflowManager { if ($this->manager === null) $this->register(); return $this->manager; }

    public function define(string $name): WorkflowDefinition
    {
        $def = new WorkflowDefinition($name);
        $this->getManager()->register($def);
        return $def;
    }
    public function definitions(): array
    {
        return $this->getManager()->all();
    }
    public function instance(string $workflowName, array $context = []): ?WorkflowInstance
    {
        return $this->getManager()->start($workflowName, $context);
    }
}
