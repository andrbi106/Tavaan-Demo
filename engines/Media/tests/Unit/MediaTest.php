<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Media\Media;
use Tavaan\Engine\Media\MimeDetector;
use Tavaan\Engine\Media\Uploader;
use Tavaan\Engine\Media\ImageProcessor;
use Tavaan\Engine\Media\Exceptions\MediaException;

/**
 * تست‌های Unit برای موتور Media.
 *
 * @see docs/03-engines/media.md (DOC-074)
 */
final class MediaTest extends TestCase
{
    public function test_media_construction(): void
    {
        $m = new Media('abc123', '/path/to/file.jpg', 'image/jpeg', 1024, ['width' => 800]);
        $this->assertSame('abc123', $m->id);
        $this->assertSame('/path/to/file.jpg', $m->path);
        $this->assertSame('image/jpeg', $m->mimeType);
        $this->assertSame(1024, $m->size);
        $this->assertTrue($m->isImage());
        $this->assertFalse($m->isVideo());
        $this->assertFalse($m->isAudio());
        $this->assertSame('jpg', $m->extension());
        $this->assertSame('file.jpg', $m->filename());
        $this->assertSame(800, $m->width());
    }

    public function test_media_is_video(): void
    {
        $m = new Media('1', 'video.mp4', 'video/mp4', 1024);
        $this->assertTrue($m->isVideo());
        $this->assertFalse($m->isImage());
    }

    public function test_media_is_audio(): void
    {
        $m = new Media('1', 'audio.mp3', 'audio/mpeg', 1024);
        $this->assertTrue($m->isAudio());
    }

    public function test_media_generate_id(): void
    {
        $id1 = Media::generateId();
        $id2 = Media::generateId();
        $this->assertSame(32, \strlen($id1));
        $this->assertNotSame($id1, $id2);
    }

    public function test_mime_detector_jpeg(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.jpg';
        \file_put_contents($tmp, "\xFF\xD8\xFF" . \str_repeat('x', 60));
        $d = new MimeDetector();
        $this->assertSame('image/jpeg', $d->detect($tmp));
        @\unlink($tmp);
    }

    public function test_mime_detector_png(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.png';
        \file_put_contents($tmp, "\x89PNG\r\n\x1A\n" . \str_repeat('x', 50));
        $d = new MimeDetector();
        $this->assertSame('image/png', $d->detect($tmp));
        @\unlink($tmp);
    }

    public function test_mime_detector_pdf(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.pdf';
        \file_put_contents($tmp, '%PDF-1.4' . \str_repeat('x', 50));
        $d = new MimeDetector();
        $this->assertSame('application/pdf', $d->detect($tmp));
        @\unlink($tmp);
    }

    public function test_mime_detector_fallback_to_extension(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.txt';
        \file_put_contents($tmp, 'plain text content');
        $d = new MimeDetector();
        $this->assertSame('text/plain', $d->detect($tmp));
        @\unlink($tmp);
    }

    public function test_mime_detector_returns_null_for_unknown(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.xyz';
        \file_put_contents($tmp, 'unknown binary data here');
        $d = new MimeDetector();
        $this->assertNull($d->detect($tmp));
        @\unlink($tmp);
    }

    public function test_mime_detector_extension_matches(): void
    {
        $d = new MimeDetector();
        $this->assertTrue($d->extensionMatchesMime('jpg', 'image/jpeg'));
        $this->assertTrue($d->extensionMatchesMime('png', 'image/png'));
        $this->assertFalse($d->extensionMatchesMime('jpg', 'image/png'));
        $this->assertFalse($d->extensionMatchesMime('php', 'image/jpeg'));
    }

    public function test_mime_detector_allowed_extensions(): void
    {
        $d = new MimeDetector();
        $exts = $d->allowedExtensions();
        $this->assertContains('jpg', $exts);
        $this->assertContains('png', $exts);
        $this->assertContains('pdf', $exts);
    }

    public function test_uploader_validates_size(): void
    {
        $u = new Uploader(maxSize: 100);
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.txt';
        \file_put_contents($tmp, \str_repeat('x', 200));
        $this->expectException(MediaException::class);
        $u->upload($tmp, 'test.txt', \sys_get_temp_dir());
        @\unlink($tmp);
    }

    public function test_uploader_rejects_disallowed_extension(): void
    {
        $u = new Uploader(allowedExtensions: ['jpg', 'png']);
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.txt';
        \file_put_contents($tmp, 'content');
        $this->expectException(MediaException::class);
        $u->upload($tmp, 'test.txt', \sys_get_temp_dir());
        @\unlink($tmp);
    }

    public function test_uploader_rejects_extension_mime_mismatch(): void
    {
        $u = new Uploader();
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.jpg';
        \file_put_contents($tmp, 'plain text but with jpg extension');
        $this->expectException(MediaException::class);
        $u->upload($tmp, 'malicious.jpg', \sys_get_temp_dir());
        @\unlink($tmp);
    }

    public function test_uploader_success(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_media_' . \uniqid();
        \mkdir($tmpDir, 0777, true);
        $source = \tempnam(\sys_get_temp_dir(), 'tvn') . '.txt';
        \file_put_contents($source, 'hello content');
        $u = new Uploader(allowedExtensions: ['txt']);
        $media = $u->upload($source, 'test.txt', $tmpDir);
        $this->assertSame('text/plain', $media->mimeType);
        $this->assertSame(13, $media->size);
        $this->assertStringEndsWith('.txt', $media->path);
        $this->assertFileExists($media->path);
        @\unlink($source);
        @\unlink($media->path);
        \rmdir($tmpDir);
    }

    public function test_uploader_renames_with_random(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_media_' . \uniqid();
        \mkdir($tmpDir, 0777, true);
        $source = \tempnam(\sys_get_temp_dir(), 'tvn') . '.txt';
        \file_put_contents($source, 'content');
        $u = new Uploader(allowedExtensions: ['txt']);
        $media = $u->upload($source, 'original_name.txt', $tmpDir);
        $this->assertStringNotContainsString('original_name', $media->filename());
        @\unlink($source);
        @\unlink($media->path);
        \rmdir($tmpDir);
    }

    public function test_uploader_validate(): void
    {
        $u = new Uploader();
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn') . '.txt';
        \file_put_contents($tmp, 'content');
        $info = $u->validate($tmp, 'test.txt');
        $this->assertSame('txt', $info['extension']);
        $this->assertSame('text/plain', $info['mime']);
        $this->assertTrue($info['valid']);
        @\unlink($tmp);
    }

    public function test_image_processor_driver_auto(): void
    {
        $p = new ImageProcessor('auto');
        $driver = $p->driver();
        $this->assertContains($driver, ['imagick', 'gd', 'none']);
    }

    public function test_image_processor_explicit_driver(): void
    {
        $p = new ImageProcessor('gd');
        $this->assertSame('gd', $p->driver());
    }

    public function test_image_processor_dimensions_no_gd(): void
    {
        // Just verify the method exists and doesn't crash
        $p = new ImageProcessor('none');
        // Without GD, dimensions() returns null
        $result = $p->dimensions('/nonexistent');
        $this->assertNull($result);
    }
}
