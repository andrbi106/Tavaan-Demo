<?php

declare(strict_types=1);

namespace Tavaan\Testing;

/**
 * Factory — builder for creating model instances for tests.
 */
final class Factory
{
    /** @var callable|null */
    private $resolver;

    private array $states = [];
    private int $count = 1;
    private array $instances = [];

    public function __construct(?callable $resolver = null)
    {
        $this->resolver = $resolver;
    }

    /**
     * definition — default attribute set; override in subclasses.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }

    /**
     * create — persist N instances and return them (or single if count=1).
     * @param array<string, mixed> $data
     * @return self|array<int, array<string, mixed>>
     */
    public function create(array $data = []): self|array
    {
        for ($i = 0; $i < $this->count; $i++) {
            $attrs = array_merge($this->definition(), $data);
            foreach ($this->states as $state) {
                $attrs = array_merge($attrs, $state);
            }
            $this->instances[] = $attrs;
            if ($this->resolver !== null) {
                ($this->resolver)($attrs);
            }
        }
        return $this;
    }

    /**
     * make — build instances without persisting.
     * @param array<string, mixed> $data
     * @return array<int, array<string, mixed>>
     */
    public function make(array $data = []): array
    {
        $built = [];
        for ($i = 0; $i < $this->count; $i++) {
            $attrs = array_merge($this->definition(), $data);
            foreach ($this->states as $state) {
                $attrs = array_merge($attrs, $state);
            }
            $built[] = $attrs;
        }
        return $built;
    }

    /**
     * count — set number of instances to build/create.
     */
    public function count(int $n): self
    {
        $this->count = max(1, $n);
        return $this;
    }

    /**
     * state — merge extra attributes for next instances.
     * @param array<string, mixed> $attrs
     */
    public function state(array $attrs): self
    {
        $this->states[] = $attrs;
        return $this;
    }

    /**
     * Get instances created so far.
     * @return array<int, array<string, mixed>>
     */
    public function getInstances(): array
    {
        return $this->instances;
    }
}
