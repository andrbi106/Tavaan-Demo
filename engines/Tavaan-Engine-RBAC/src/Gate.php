<?php

declare(strict_types=1);

namespace Tavaan\Engine\Rbac;

final class Gate
{
    private array $abilities = [];
    private array $policies = [];
    private array $beforeCallbacks = [];
    private array $afterCallbacks = [];
    private mixed $user = null;

    public function __construct(?callable $userResolver = null)
    {
        if ($userResolver !== null) {
            $this->user = $userResolver();
        }
    }

    public function define(string $ability, callable $callback): self
    {
        $this->abilities[$ability] = $callback;
        return $this;
    }

    public function policy(string $class, string $policyClass): self
    {
        $this->policies[$class] = $policyClass;
        return $this;
    }

    public function before(callable $callback): self
    {
        $this->beforeCallbacks[] = $callback;
        return $this;
    }

    public function after(callable $callback): self
    {
        $this->afterCallbacks[] = $callback;
        return $this;
    }

    public function allows(string $ability, array $arguments = []): bool
    {
        return $this->check($ability, $arguments);
    }

    public function denies(string $ability, array $arguments = []): bool
    {
        return !$this->allows($ability, $arguments);
    }

    public function check(string $ability, array $arguments = []): bool
    {
        foreach ($this->beforeCallbacks as $before) {
            $result = $before($this->user, $ability, $arguments);
            if ($result !== null) return $result;
        }

        if (isset($this->abilities[$ability])) {
            $result = ($this->abilities[$ability])($this->user, ...$arguments);
            return (bool) $result;
        }

        foreach ($this->afterCallbacks as $after) {
            $result = $after($this->user, $ability, $arguments, null);
            if ($result !== null) return $result;
        }

        return false;
    }

    public function any(array $abilities, array $arguments = []): bool
    {
        foreach ($abilities as $ability) {
            if ($this->check($ability, $arguments)) return true;
        }
        return false;
    }

    public function authorize(string $ability, array $arguments = []): void
    {
        if (!$this->check($ability, $arguments)) {
            throw new \RuntimeException("Ability '{$ability}' is not allowed.");
        }
    }

    public function forUser(mixed $user): self
    {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function abilities(): array
    {
        return array_keys($this->abilities);
    }
}
