<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem\Contracts;

/**
 * قرارداد Filesystem.
 *
 * @api 1.0
 */
interface FilesystemInterface
{
    public function exists(string $path): bool;

    public function get(string $path): ?string;

    public function put(string $path, string $content, string $visibility = 'private'): bool;

    public function delete(string $path): bool;

    public function copy(string $from, string $to): bool;

    public function move(string $from, string $to): bool;

    public function size(string $path): int;

    public function lastModified(string $path): int;

    public function mimeType(string $path): ?string;

    public function append(string $path, string $content): bool;

    public function prepend(string $path, string $content): bool;

    /**
     * @return list<string>
     */
    public function files(string $directory): array;

    /**
     * @return list<string>
     */
    public function allFiles(string $directory): array;

    /**
     * @return list<string>
     */
    public function directories(string $directory): array;

    /**
     * @return list<string>
     */
    public function allDirectories(string $directory): array;

    public function makeDirectory(string $path): bool;

    public function deleteDirectory(string $path): bool;

    public function visibility(string $path): string;

    public function setVisibility(string $path, string $visibility): bool;

    public function readStream(string $path): mixed;

    public function writeStream(string $path, $stream): bool;
}
