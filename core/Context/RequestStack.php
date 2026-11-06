<?php
declare(strict_types=1);
namespace Tavaan\Core\Context;

/**
 * Stack از requestها — برای nested requests (مثلاً sub-request در tests).
 */
final class RequestStack
{
    /** @var list<mixed> */
    private array $stack = [];

    public function push(mixed $request): void
    {
        $this->stack[] = $request;
    }

    public function pop(): mixed
    {
        if (empty($this->stack)) {
            return null;
        }
        return array_pop($this->stack);
    }

    public function current(): mixed
    {
        return end($this->stack) ?: null;
    }

    public function isEmpty(): bool
    {
        return empty($this->stack);
    }
}
