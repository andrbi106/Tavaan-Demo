<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem\Drivers;

use Tavaan\Engine\Filesystem\Contracts\FilesystemInterface;
use Tavaan\Engine\Filesystem\Exceptions\FilesystemException;

/**
 * LocalFilesystem — درایور فایل‌سیستم محلی.
 *
 * 🔒 Sandbox: path باید داخل root باشد (با realpath بررسی می‌شود).
 *
 * @see docs/03-engines/filesystem.md
 */
final class LocalFilesystem implements FilesystemInterface
{
    public function __construct(
        private readonly string $root,
        private readonly string $defaultVisibility = 'private',
    ) {
        if (!\is_dir($root)) {
            \mkdir($root, 0777, true);
        }
        $this->root = \rtrim(\realpath($root), '/');
    }

    public function exists(string $path): bool
    {
        return \file_exists($this->resolve($path));
    }

    public function get(string $path): ?string
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return null;
        }
        $content = @\file_get_contents($full);
        return $content === false ? null : $content;
    }

    public function put(string $path, string $content, string $visibility = 'private'): bool
    {
        $full = $this->resolve($path, mkdir: true);
        $result = @\file_put_contents($full, $content);
        if ($result === false) {
            return false;
        }
        $this->setVisibility($path, $visibility);
        return true;
    }

    public function delete(string $path): bool
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return true;
        }
        return @\unlink($full);
    }

    public function copy(string $from, string $to): bool
    {
        $src = $this->resolve($from);
        $dst = $this->resolve($to, mkdir: true);
        if (!\file_exists($src)) {
            return false;
        }
        return @\copy($src, $dst);
    }

    public function move(string $from, string $to): bool
    {
        $src = $this->resolve($from);
        $dst = $this->resolve($to, mkdir: true);
        if (!\file_exists($src)) {
            return false;
        }
        return @\rename($src, $dst);
    }

    public function size(string $path): int
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return 0;
        }
        return (int) \filesize($full);
    }

    public function lastModified(string $path): int
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return 0;
        }
        return (int) \filemtime($full);
    }

    public function mimeType(string $path): ?string
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return null;
        }
        if (\function_exists('mime_content_type')) {
            $mime = \mime_content_type($full);
            return $mime === false ? null : $mime;
        }
        if (\function_exists('finfo_file')) {
            $finfo = \finfo_open(\FILEINFO_MIME_TYPE);
            $mime = \finfo_file($finfo, $full);
            \finfo_close($finfo);
            return $mime === false ? null : $mime;
        }
        return null;
    }

    public function append(string $path, string $content): bool
    {
        $full = $this->resolve($path, mkdir: true);
        $result = @\file_put_contents($full, $content, \FILE_APPEND);
        return $result !== false;
    }

    public function prepend(string $path, string $content): bool
    {
        $existing = $this->get($path) ?? '';
        return $this->put($path, $content . $existing);
    }

    public function files(string $directory): array
    {
        $full = $this->resolve($directory);
        if (!\is_dir($full)) {
            return [];
        }
        $result = [];
        foreach (\scandir($full) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $path = $directory . '/' . $entry;
            if (\is_file($this->resolve($path))) {
                $result[] = $path;
            }
        }
        return $result;
    }

    public function allFiles(string $directory): array
    {
        $result = [];
        foreach ($this->files($directory) as $file) {
            $result[] = $file;
        }
        foreach ($this->directories($directory) as $dir) {
            $result = \array_merge($result, $this->allFiles($dir));
        }
        return $result;
    }

    public function directories(string $directory): array
    {
        $full = $this->resolve($directory);
        if (!\is_dir($full)) {
            return [];
        }
        $result = [];
        foreach (\scandir($full) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $path = $directory . '/' . $entry;
            if (\is_dir($this->resolve($path))) {
                $result[] = $path;
            }
        }
        return $result;
    }

    public function allDirectories(string $directory): array
    {
        $result = [];
        foreach ($this->directories($directory) as $dir) {
            $result[] = $dir;
            $result = \array_merge($result, $this->allDirectories($dir));
        }
        return $result;
    }

    public function makeDirectory(string $path): bool
    {
        $full = $this->resolve($path);
        if (\is_dir($full)) {
            return true;
        }
        return @\mkdir($full, 0777, true);
    }

    public function deleteDirectory(string $path): bool
    {
        $full = $this->resolve($path);
        if (!\is_dir($full)) {
            return true;
        }
        foreach ($this->allFiles($path) as $file) {
            $this->delete($file);
        }
        foreach ($this->allDirectories($path) as $dir) {
            @\rmdir($this->resolve($dir));
        }
        return @\rmdir($full);
    }

    public function visibility(string $path): string
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return $this->defaultVisibility;
        }
        $perms = \fileperms($full);
        if ($perms === false) {
            return $this->defaultVisibility;
        }
        return ($perms & 0044) ? 'public' : 'private';
    }

    public function setVisibility(string $path, string $visibility): bool
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return false;
        }
        $mode = $visibility === 'public' ? 0644 : 0600;
        if (\is_dir($full)) {
            $mode = $visibility === 'public' ? 0755 : 0700;
        }
        return @\chmod($full, $mode);
    }

    public function readStream(string $path): mixed
    {
        $full = $this->resolve($path);
        if (!\file_exists($full)) {
            return null;
        }
        return \fopen($full, 'rb') ?: null;
    }

    public function writeStream(string $path, $stream): bool
    {
        $full = $this->resolve($path, mkdir: true);
        $out = \fopen($full, 'wb');
        if ($out === false) {
            return false;
        }
        while (!\feof($stream)) {
            $chunk = \fread($stream, 8192);
            if ($chunk === false) break;
            \fwrite($out, $chunk);
        }
        \fclose($out);
        return true;
    }

    /**
     * 🔒 Resolve path و بررسی sandbox.
     */
    private function resolve(string $path, bool $mkdir = false): string
    {
        $path = \ltrim($path, '/');
        $full = $this->root . '/' . $path;
        $real = \realpath($full);
        if ($real !== false) {
            // Already exists — verify it's inside root
            if (!\str_starts_with($real, $this->root)) {
                throw FilesystemException::pathOutsideRoot($path);
            }
            return $real;
        }
        // Doesn't exist — check parent dir
        if ($mkdir) {
            $dir = \dirname($full);
            if (!\is_dir($dir)) {
                @\mkdir($dir, 0777, true);
            }
        }
        $parent = \realpath(\dirname($full));
        if ($parent === false || !\str_starts_with($parent, $this->root)) {
            throw FilesystemException::pathOutsideRoot($path);
        }
        return $full;
    }

    public function getRoot(): string
    {
        return $this->root;
    }
}
