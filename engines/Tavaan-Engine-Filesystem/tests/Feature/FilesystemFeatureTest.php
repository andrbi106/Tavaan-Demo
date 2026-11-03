<?php

declare(strict_types=1);

namespace Tavaan\Engine\Filesystem\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Filesystem\FilesystemManager;
use Tavaan\Engine\Filesystem\FilesystemServiceProvider;
use Tavaan\Engine\Filesystem\Contracts\FilesystemInterface;

/**
 * تست‌های Feature برای موتور Filesystem.
 */
final class FilesystemFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new FilesystemServiceProvider([
            'default' => 'memory',
            'disks' => ['memory' => ['driver' => 'memory']],
        ]);
        $provider->register();
        $m = $provider->getManager();
        $this->assertInstanceOf(FilesystemManager::class, $m);
        $this->assertInstanceOf(FilesystemInterface::class, $m);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new FilesystemServiceProvider([]);
        $this->assertContains(FilesystemInterface::class, $provider->provides());
        $this->assertContains(FilesystemManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/filesystem.php';
        $this->assertArrayHasKey('default', $config);
        $this->assertArrayHasKey('disks', $config);
        $this->assertArrayHasKey('local', $config['disks']);
        $this->assertArrayHasKey('public', $config['disks']);
        $this->assertArrayHasKey('memory', $config['disks']);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/filesystem.php';
        $this->assertArrayHasKey('error_path_outside_root', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/filesystem.php';
        $this->assertArrayHasKey('error_path_outside_root', $messages);
    }

    public function test_end_to_end_disk_operations(): void
    {
        $provider = new FilesystemServiceProvider([
            'default' => 'memory',
            'disks' => ['memory' => ['driver' => 'memory']],
        ]);
        $provider->register();
        $m = $provider->getManager();

        $m->put('file1.txt', 'content1');
        $m->put('dir/file2.txt', 'content2');
        $this->assertSame('content1', $m->get('file1.txt'));
        $this->assertSame('content2', $m->get('dir/file2.txt'));
        $this->assertCount(2, $m->allFiles(''));
    }
}
