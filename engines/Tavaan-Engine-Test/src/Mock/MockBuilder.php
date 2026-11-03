<?php

declare(strict_types=1);

namespace Tavaan\Testing\Mock;

/**
 * MockBuilder — ساخت یک mock object با expectations.
 *
 * @see docs/03-engines/test.md §4.1
 */
final class MockBuilder
{
    /** @var array<string, list<Expectation>> */
    private array $expectations = [];

    public function __construct(
        private readonly string $interface,
    ) {
    }

    public function shouldReceive(string $method): Expectation
    {
        $expectation = new Expectation($method);
        $this->expectations[$method][] = $expectation;
        return $expectation;
    }

    public function getMock(): object
    {
        $expectations = $this->expectations;
        $interface = $this->interface;
        return new class ($expectations) {
            /**
             * @param array<string, list<Expectation>> $expectations
             */
            public function __construct(private array $expectations)
            {
            }

            public function __call(string $method, array $args): mixed
            {
                if (!isset($this->expectations[$method])) {
                    throw new \RuntimeException("No expectation set for method '{$method}'.");
                }
                foreach ($this->expectations[$method] as $expectation) {
                    if ($expectation->matches($args)) {
                        return $expectation->execute($args);
                    }
                }
                throw new \RuntimeException("No matching expectation for '{$method}' with args " . \json_encode($args));
            }
        };
    }
}
