<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Media\Media;
use Tavaan\Engine\Media\MimeDetector;
use Tavaan\Engine\Media\Uploader;
use Tavaan\Engine\Media\ImageProcessor;
use Tavaan\Engine\Media\MediaServiceProvider;

/**
 * تست‌های Feature برای موتور Media.
 */
final class MediaFeatureTest extends TestCase
{
    public function test_service_provider_creates_services(): void
    {
        $provider = new MediaServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(MimeDetector::class, $provider->getDetector());
        $this->assertInstanceOf(Uploader::class, $provider->getUploader());
        $this->assertInstanceOf(ImageProcessor::class, $provider->getProcessor());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new MediaServiceProvider([]);
        $this->assertContains(MimeDetector::class, $provider->provides());
        $this->assertContains(Uploader::class, $provider->provides());
        $this->assertContains(ImageProcessor::class, $provider->provides());
        $this->assertContains(Media::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/media.php';
        $this->assertArrayHasKey('max_size', $config);
        $this->assertArrayHasKey('allowed_extensions', $config);
        $this->assertArrayHasKey('image_driver', $config);
        $this->assertArrayHasKey('thumbnails', $config);
        $this->assertArrayHasKey('webp', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/media.php';
        $this->assertArrayHasKey('error_upload', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/media.php';
        $this->assertArrayHasKey('error_upload', $messages);
    }

    public function test_end_to_end_upload_workflow(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_media_' . \uniqid();
        \mkdir($tmpDir, 0777, true);
        $source = \tempnam(\sys_get_temp_dir(), 'tvn') . '.txt';
        \file_put_contents($source, 'hello content');

        $provider = new MediaServiceProvider([]);
        $provider->register();
        $uploader = $provider->getUploader();
        $media = $uploader->upload($source, 'test.txt', $tmpDir);
        $this->assertSame('text/plain', $media->mimeType);

        @\unlink($source);
        @\unlink($media->path);
        \rmdir($tmpDir);
    }
}
