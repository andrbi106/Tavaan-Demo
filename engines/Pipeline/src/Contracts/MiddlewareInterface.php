<?php

declare(strict_types=1);

namespace Tavaan\Engine\Pipeline\Contracts;

/**
 * قرارداد عمومی Pipe (Middleware).
 *
 * @see docs/03-engines/pipeline.md §2.2
 */
interface MiddlewareInterface
{
    /**
     * @param mixed $passable
     * @param \Closure(mixed): mixed $next
     * @param string ...$params
     */
    public function handle(mixed $passable, \Closure $next, string ...$params): mixed;

    /**
     * پس از اتمام زنجیره (اختیاری).
     *
     * @param mixed $passable
     * @param mixed $result
     */
    public function terminate(mixed $passable, mixed $result): void;
}
