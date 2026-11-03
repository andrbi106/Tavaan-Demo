<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem\Drivers;

use Tavaan\Engine\Filesystem\Contracts\FilesystemInterface;

/**
 * MemoryFilesystem — درایور درون‌حافظه‌ای (برای تست).
 */
final class MemoryFilesystem implements FilesystemInterface
{
    /**
     * @var array<string, array{content: string, mtime: int, vis: string}>
     */
    private array $files = [];

    /**
     * @var list<string>
     */
    private array $dirs = [];

    public function exists(string $path): bool
    {
        return isset($this->files[$path]) || \in_array($path, $this->dirs, true);
    }

    public function get(string $path): ?string
    {
        return $this->files[$path]['content'] ?? null;
    }

    public function put(string $path, string $content, string $visibility = 'private'): bool
    {
        $this->files[$path] = [
            'content' => $content,
            'mtime' => \time(),
            'vis' => $visibility,
        ];
        return true;
    }

    public function delete(string $path): bool
    {
        unset($this->files[$path]);
        return true;
    }

    public function copy(string $from, string $to): bool
    {
        if (!isset($this->files[$from])) return false;
        $this->files[$to] = $this->files[$from];
        return true;
    }

    public function move(string $from, string $to): bool
    {
        if (!$this->copy($from, $to)) return false;
        $this->delete($from);
        return true;
    }

    public function size(string $path): int
    {
        return isset($this->files[$path]) ? \strlen($this->files[$path]['content']) : 0;
    }

    public function lastModified(string $path): int
    {
        return $this->files[$path]['mtime'] ?? 0;
    }

    public function mimeType(string $path): ?string
    {
        if (!isset($this->files[$path])) return null;
        $content = $this->files[$path]['content'];
        // Basic detection based on first bytes
        if (\str_starts_with($content, "\xFF\xD8\xFF")) return 'image/jpeg';
        if (\str_starts_with($content, "\x89PNG")) return 'image/png';
        if (\str_starts_with($content, "GIF8")) return 'image/gif';
        if (\str_starts_with($content, '%PDF-')) return 'application/pdf';
        return 'application/octet-stream';
    }

    public function append(string $path, string $content): bool
    {
        $existing = $this->get($path) ?? '';
        return $this->put($path, $existing . $content);
    }

    public function prepend(string $path, string $content): bool
    {
        $existing = $this->get($path) ?? '';
        return $this->put($path, $content . $existing);
    }

    public function files(string $directory): array
    {
        $directory = \rtrim($directory, '/');
        $result = [];
        foreach (\array_keys($this->files) as $path) {
            if (\str_starts_with($path, $directory . '/') && !\str_contains(\substr($path, \strlen($directory) + 1), '/')) {
                $result[] = $path;
            }
        }
        return $result;
    }

    public function allFiles(string $directory): array
    {
        $directory = \rtrim($directory, '/');
        $result = [];
        foreach (\array_keys($this->files) as $path) {
            if (\str_starts_with($path, $directory . '/')) {
                $result[] = $path;
            }
        }
        return $result;
    }

    public function directories(string $directory): array
    {
        $directory = \rtrim($directory, '/');
        $result = [];
        foreach ($this->dirs as $dir) {
            if (\str_starts_with($dir, $directory . '/') && !\str_contains(\substr($dir, \strlen($directory) + 1), '/')) {
                $result[] = $dir;
            }
        }
        return $result;
    }

    public function allDirectories(string $directory): array
    {
        $directory = \rtrim($directory, '/');
        $result = [];
        foreach ($this->dirs as $dir) {
            if (\str_starts_with($dir, $directory . '/')) {
                $result[] = $dir;
            }
        }
        return $result;
    }

    public function makeDirectory(string $path): bool
    {
        if (\in_array($path, $this->dirs, true)) return true;
        $this->dirs[] = $path;
        return true;
    }

    public function deleteDirectory(string $path): bool
    {
        $this->dirs = \array_values(\array_filter($this->dirs, fn($d) => $d !== $path && !\str_starts_with($d, $path . '/')));
        foreach (\array_keys($this->files) as $f) {
            if (\str_starts_with($f, $path . '/')) {
                unset($this->files[$f]);
            }
        }
        return true;
    }

    public function visibility(string $path): string
    {
        return $this->files[$path]['vis'] ?? 'private';
    }

    public function setVisibility(string $path, string $visibility): bool
    {
        if (isset($this->files[$path])) {
            $this->files[$path]['vis'] = $visibility;
            return true;
        }
        return false;
    }

    public function readStream(string $path): mixed
    {
        $content = $this->get($path);
        if ($content === null) return null;
        $stream = \fopen('php://memory', 'r+');
        \fwrite($stream, $content);
        \rewind($stream);
        return $stream;
    }

    public function writeStream(string $path, $stream): bool
    {
        $content = \stream_get_contents($stream);
        if ($content === false) return false;
        return $this->put($path, $content);
    }
}
