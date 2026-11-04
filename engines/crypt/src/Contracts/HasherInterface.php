<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Contracts;

/**
 * تَوان | Tavaan — Contracts
 * HasherInterface — قرارداد درهم‌ساز رمز عبور (DOC-014 §2.8 / DOC-055).
 *
 * @api 1.0
 * @see docs/03-engines/hash.md
 */
interface HasherInterface
{
    public function make(#[\SensitiveParameter] string $value): string;

    public function check(#[\SensitiveParameter] string $value, string $hash): bool;

    public function needsRehash(string $hash): bool;

    /** @return array<string, mixed> */
    public function info(string $hash): array;
}
