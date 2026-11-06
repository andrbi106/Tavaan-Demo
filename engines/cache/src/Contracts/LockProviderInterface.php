<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Contracts;

/**
 * تَوان | Tavaan — Contracts
 * LockProviderInterface — قرارداد ارائه‌دهندهٔ قفل (DOC-014 §2.5).
 *
 * @api 1.0
 * @see docs/03-engines/cache.md
 */
interface LockProviderInterface
{
    public function lock(string $name, int $seconds = 0, ?string $owner = null): LockInterface;

    public function restoreLock(string $name, string $owner): LockInterface;
}
