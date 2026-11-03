<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat\Contracts;

/**
 * قرارداد ZipWriterPure.
 *
 * @see \Tavaan\Engine\Compat\ZipWriterPure
 * @api 1.0
 */
interface ZipWriterPureInterface
{
    public function addFromString(string $name, string $data, int $level = 6): void;

    public function addFile(string $path, ?string $name = null): void;

    public function addDirectory(string $name): void;

    public function output(): string;

    public function saveTo(string $path): void;

    public function reset(): void;
}
