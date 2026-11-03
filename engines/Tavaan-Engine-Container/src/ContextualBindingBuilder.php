<?php

declare(strict_types=1);

namespace Tavaan\Engine\Container;

/**
 * Contextual binding builder — fluent API for `when()->needs()->give()`.
 *
 * @see docs/03-engines/container.md §4
 */
final class ContextualBindingBuilder
{
    public function __construct(
        private readonly Container $container,
        private readonly string $consumer,
    ) {
    }

    /**
     * Define what dependency the consumer needs.
     *
     * @param string $param parameter name or abstract type
     */
    public function needs(string $param): self
    {
        $this->param = $param;
        return $this;
    }

    private string $param = '';

    /**
     * Provide the binding value (closure or class-string).
     */
    public function give(\Closure|string $value): void
    {
        $this->container->addContextualBinding($this->consumer, $this->param, $value);
    }
}
