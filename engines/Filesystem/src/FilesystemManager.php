<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem;

use Tavaan\Engine\Filesystem\Contracts\FilesystemInterface;
use Tavaan\Engine\Filesystem\Drivers\LocalFilesystem;
use Tavaan\Engine\Filesystem\Drivers\MemoryFilesystem;

/**
 * FilesystemManager — مدیریت چند disk.
 */
final class FilesystemManager implements FilesystemInterface
{
    private FilesystemInterface $default;

    /**
     * @var array<string, FilesystemInterface>
     */
    private array $disks = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
        $defaultName = $config['default'] ?? 'local';
        $this->default = $this->createDisk($defaultName, $config['disks'][$defaultName] ?? []);
    }

    public function disk(string $name): FilesystemInterface
    {
        if (!isset($this->disks[$name])) {
            $this->disks[$name] = $this->createDisk($name, $this->config['disks'][$name] ?? []);
        }
        return $this->disks[$name];
    }

    public function exists(string $path): bool { return $this->default->exists($path); }
    public function get(string $path): ?string { return $this->default->get($path); }
    public function put(string $path, string $content, string $visibility = 'private'): bool
    {
        return $this->default->put($path, $content, $visibility);
    }
    public function delete(string $path): bool { return $this->default->delete($path); }
    public function copy(string $from, string $to): bool { return $this->default->copy($from, $to); }
    public function move(string $from, string $to): bool { return $this->default->move($from, $to); }
    public function size(string $path): int { return $this->default->size($path); }
    public function lastModified(string $path): int { return $this->default->lastModified($path); }
    public function mimeType(string $path): ?string { return $this->default->mimeType($path); }
    public function append(string $path, string $content): bool { return $this->default->append($path, $content); }
    public function prepend(string $path, string $content): bool { return $this->default->prepend($path, $content); }
    public function files(string $directory): array { return $this->default->files($directory); }
    public function allFiles(string $directory): array { return $this->default->allFiles($directory); }
    public function directories(string $directory): array { return $this->default->directories($directory); }
    public function allDirectories(string $directory): array { return $this->default->allDirectories($directory); }
    public function makeDirectory(string $path): bool { return $this->default->makeDirectory($path); }
    public function deleteDirectory(string $path): bool { return $this->default->deleteDirectory($path); }
    public function visibility(string $path): string { return $this->default->visibility($path); }
    public function setVisibility(string $path, string $visibility): bool
    {
        return $this->default->setVisibility($path, $visibility);
    }
    public function readStream(string $path): mixed { return $this->default->readStream($path); }
    public function writeStream(string $path, $stream): bool { return $this->default->writeStream($path, $stream); }

    public function getDefaultDisk(): FilesystemInterface
    {
        return $this->default;
    }

    /**
     * @param array<string, mixed> $cfg
     */
    private function createDisk(string $name, array $cfg): FilesystemInterface
    {
        $driver = $cfg['driver'] ?? 'local';
        return match ($driver) {
            'memory' => new MemoryFilesystem(),
            default => new LocalFilesystem(
                root: $cfg['root'] ?? \sys_get_temp_dir() . "/tvn_fs_{$name}",
                defaultVisibility: $cfg['visibility'] ?? 'private',
            ),
        };
    }
}
