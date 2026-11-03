<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash\Contracts;

/**
 * قرارداد Hasher برای رمز عبور.
 *
 * @see \Tavaan\Engine\Hash\HashManager
 * @api 1.0
 */
interface HasherInterface
{
    public function make(string $value, array $options = []): string;

    public function check(string $value, string $hash): bool;

    public function needsRehash(string $hash, array $options = []): bool;

    /**
     * @return array<string, mixed>
     */
    public function info(string $hash): array;
}
