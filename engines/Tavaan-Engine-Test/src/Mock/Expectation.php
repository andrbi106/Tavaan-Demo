<?php

declare(strict_types=1);

namespace Tavaan\Testing\Mock;

/**
 * Expectation — انتظار یک فراخوانی متد خاص با آرگومان‌های خاص.
 */
final class Expectation
{
    public function __construct(
        public readonly string $method,
        public ?array $expectedArgs = null,
        public mixed $returnValue = null,
        public ?\Closure $callback = null,
        public int $timesExpected = -1,
        public int $timesCalled = 0,
    ) {
    }

    public function with(mixed ...$args): self
    {
        $this->expectedArgs = $args;
        return $this;
    }

    public function andReturn(mixed $value): self
    {
        $this->returnValue = $value;
        return $this;
    }

    public function andReturnUsing(\Closure $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function once(): self
    {
        $this->timesExpected = 1;
        return $this;
    }

    public function times(int $n): self
    {
        $this->timesExpected = $n;
        return $this;
    }

    public function matches(array $args): bool
    {
        if ($this->expectedArgs === null) {
            return true;
        }
        if (\count($args) !== \count($this->expectedArgs)) {
            return false;
        }
        foreach ($this->expectedArgs as $i => $expected) {
            if ($args[$i] !== $expected) {
                return false;
            }
        }
        return true;
    }

    public function execute(array $args): mixed
    {
        $this->timesCalled++;
        if ($this->callback !== null) {
            return ($this->callback)(...$args);
        }
        return $this->returnValue;
    }
}
