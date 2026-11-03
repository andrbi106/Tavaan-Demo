<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Filesystem\FilesystemManager;
use Tavaan\Engine\Filesystem\Drivers\LocalFilesystem;
use Tavaan\Engine\Filesystem\Drivers\MemoryFilesystem;
use Tavaan\Engine\Filesystem\Exceptions\FilesystemException;
use Tavaan\Engine\Filesystem\Contracts\FilesystemInterface;

/**
 * تست‌های Unit برای موتور Filesystem.
 *
 * @see docs/03-engines/filesystem.md (DOC-063)
 */
final class FilesystemTest extends TestCase
{
    private function tmpRoot(): string
    {
        $root = \sys_get_temp_dir() . '/tvn_fs_' . \uniqid();
        \mkdir($root, 0777, true);
        return $root;
    }

    public function test_local_put_and_get(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('hello.txt', 'Hello, World!');
        $this->assertSame('Hello, World!', $fs->get('hello.txt'));
    }

    public function test_local_exists(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('test.txt', 'content');
        $this->assertTrue($fs->exists('test.txt'));
        $this->assertFalse($fs->exists('missing.txt'));
    }

    public function test_local_get_missing_returns_null(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $this->assertNull($fs->get('missing.txt'));
    }

    public function test_local_delete(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('file.txt', 'x');
        $this->assertTrue($fs->delete('file.txt'));
        $this->assertFalse($fs->exists('file.txt'));
        $this->assertTrue($fs->delete('already_deleted'));  // idempotent
    }

    public function test_local_size(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('test.txt', '12345');
        $this->assertSame(5, $fs->size('test.txt'));
        $this->assertSame(0, $fs->size('missing.txt'));
    }

    public function test_local_copy(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('a.txt', 'content');
        $this->assertTrue($fs->copy('a.txt', 'b.txt'));
        $this->assertSame('content', $fs->get('b.txt'));
    }

    public function test_local_move(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('a.txt', 'content');
        $this->assertTrue($fs->move('a.txt', 'b.txt'));
        $this->assertFalse($fs->exists('a.txt'));
        $this->assertSame('content', $fs->get('b.txt'));
    }

    public function test_local_append(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('log.txt', 'line1\n');
        $fs->append('log.txt', 'line2\n');
        $this->assertSame('line1\nline2\n', $fs->get('log.txt'));
    }

    public function test_local_prepend(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('log.txt', 'existing');
        $fs->prepend('log.txt', 'pre ');
        $this->assertSame('pre existing', $fs->get('log.txt'));
    }

    public function test_local_subdirectory(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('dir/sub/file.txt', 'nested');
        $this->assertSame('nested', $fs->get('dir/sub/file.txt'));
    }

    public function test_local_make_directory(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->makeDirectory('new/dir');
        $this->assertTrue(\is_dir($fs->getRoot() . '/new/dir'));
    }

    public function test_local_files_list(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('a.txt', 'a');
        $fs->put('b.txt', 'b');
        $fs->makeDirectory('sub');
        $files = $fs->files('/');
        $this->assertContains('/a.txt', $files);
        $this->assertContains('/b.txt', $files);
    }

    public function test_local_all_files_recursive(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('a.txt', 'a');
        $fs->put('sub/b.txt', 'b');
        $fs->put('sub/deep/c.txt', 'c');
        $all = $fs->allFiles('/');
        $this->assertCount(3, $all);
    }

    public function test_local_delete_directory(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('dir/a.txt', 'a');
        $fs->put('dir/sub/b.txt', 'b');
        $this->assertTrue($fs->deleteDirectory('dir'));
        $this->assertFalse($fs->exists('dir/a.txt'));
    }

    public function test_local_mime_type(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('file.txt', 'text content');
        $mime = $fs->mimeType('file.txt');
        // mime_content_type should return text/plain
        $this->assertNotNull($mime);
    }

    public function test_local_visibility(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $fs->put('public.txt', 'x', 'public');
        $fs->put('private.txt', 'x', 'private');
        $this->assertSame('public', $fs->visibility('public.txt'));
        $this->assertSame('private', $fs->visibility('private.txt'));
    }

    public function test_local_read_write_stream(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $stream = \fopen('php://memory', 'r+');
        \fwrite($stream, 'streamed content');
        \rewind($stream);
        $this->assertTrue($fs->writeStream('test.txt', $stream));
        \fclose($stream);

        $read = $fs->readStream('test.txt');
        $this->assertIsResource($read);
        $content = \stream_get_contents($read);
        $this->assertSame('streamed content', $content);
        \fclose($read);
    }

    public function test_path_outside_root_throws(): void
    {
        $fs = new LocalFilesystem($this->tmpRoot());
        $this->expectException(FilesystemException::class);
        // Try to use .. to escape root
        $fs->get('../../etc/passwd');
    }

    public function test_local_creates_root_if_missing(): void
    {
        $root = \sys_get_temp_dir() . '/tvn_fs_auto_' . \uniqid();
        // Don't create; constructor should create it
        $fs = new LocalFilesystem($root);
        $this->assertDirectoryExists($fs->getRoot());
    }

    // === MemoryFilesystem ===

    public function test_memory_put_and_get(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('hello.txt', 'Hello!');
        $this->assertSame('Hello!', $fs->get('hello.txt'));
    }

    public function test_memory_exists(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('a.txt', 'x');
        $this->assertTrue($fs->exists('a.txt'));
        $this->assertFalse($fs->exists('b.txt'));
    }

    public function test_memory_delete(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('a.txt', 'x');
        $this->assertTrue($fs->delete('a.txt'));
        $this->assertNull($fs->get('a.txt'));
    }

    public function test_memory_copy(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('a.txt', 'content');
        $this->assertTrue($fs->copy('a.txt', 'b.txt'));
        $this->assertSame('content', $fs->get('b.txt'));
        $this->assertNotNull($fs->get('a.txt'));
    }

    public function test_memory_move(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('a.txt', 'content');
        $this->assertTrue($fs->move('a.txt', 'b.txt'));
        $this->assertNull($fs->get('a.txt'));
        $this->assertSame('content', $fs->get('b.txt'));
    }

    public function test_memory_append(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('log.txt', 'line1');
        $fs->append('log.txt', 'line2');
        $this->assertSame('line1line2', $fs->get('log.txt'));
    }

    public function test_memory_visibility(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('a.txt', 'x', 'public');
        $this->assertSame('public', $fs->visibility('a.txt'));
        $fs->setVisibility('a.txt', 'private');
        $this->assertSame('private', $fs->visibility('a.txt'));
    }

    public function test_memory_files(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('dir/a.txt', 'a');
        $fs->put('dir/b.txt', 'b');
        $fs->put('dir/sub/c.txt', 'c');
        $files = $fs->files('dir');
        $this->assertCount(2, $files);
        $all = $fs->allFiles('dir');
        $this->assertCount(3, $all);
    }

    public function test_memory_directories(): void
    {
        $fs = new MemoryFilesystem();
        $fs->makeDirectory('dir');
        $fs->makeDirectory('dir/sub');
        $fs->makeDirectory('other');
        $dirs = $fs->directories('');
        $this->assertContains('dir', $dirs);
        $this->assertContains('other', $dirs);
        $all = $fs->allDirectories('');
        $this->assertCount(3, $all);
    }

    public function test_memory_delete_directory(): void
    {
        $fs = new MemoryFilesystem();
        $fs->makeDirectory('dir');
        $fs->put('dir/a.txt', 'a');
        $fs->put('dir/sub/b.txt', 'b');
        $this->assertTrue($fs->deleteDirectory('dir'));
        $this->assertNull($fs->get('dir/a.txt'));
    }

    public function test_memory_mime_type(): void
    {
        $fs = new MemoryFilesystem();
        $fs->put('image.jpg', "\xFF\xD8\xFF");
        $this->assertSame('image/jpeg', $fs->mimeType('image.jpg'));
    }

    public function test_memory_stream(): void
    {
        $fs = new MemoryFilesystem();
        $s = \fopen('php://memory', 'r+');
        \fwrite($s, 'content');
        \rewind($s);
        $fs->writeStream('a.txt', $s);
        \fclose($s);
        $this->assertSame('content', $fs->get('a.txt'));

        $r = $fs->readStream('a.txt');
        $this->assertSame('content', \stream_get_contents($r));
        \fclose($r);
    }

    // === FilesystemManager ===

    public function test_manager_default_disk(): void
    {
        $m = new FilesystemManager([
            'default' => 'memory',
            'disks' => ['memory' => ['driver' => 'memory']],
        ]);
        $m->put('a.txt', 'content');
        $this->assertSame('content', $m->get('a.txt'));
    }

    public function test_manager_named_disk(): void
    {
        $m = new FilesystemManager([
            'default' => 'memory',
            'disks' => [
                'memory' => ['driver' => 'memory'],
                'public' => ['driver' => 'memory'],
            ],
        ]);
        $public = $m->disk('public');
        $this->assertInstanceOf(FilesystemInterface::class, $public);
    }

    public function test_manager_implements_interface(): void
    {
        $m = new FilesystemManager([]);
        $this->assertInstanceOf(FilesystemInterface::class, $m);
    }

    public function test_manager_local_disk_default(): void
    {
        $m = new FilesystemManager([
            'default' => 'local',
            'disks' => ['local' => ['driver' => 'local', 'root' => $this->tmpRoot()]],
        ]);
        $m->put('test.txt', 'hello');
        $this->assertSame('hello', $m->get('test.txt'));
    }
}
