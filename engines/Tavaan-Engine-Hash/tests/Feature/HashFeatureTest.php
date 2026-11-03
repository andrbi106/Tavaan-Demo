<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Hash\HashManager;
use Tavaan\Engine\Hash\HashServiceProvider;
use Tavaan\Engine\Hash\Contracts\HasherInterface;

/**
 * تست‌های Feature برای موتور Hash.
 */
final class HashFeatureTest extends TestCase
{
    public function test_service_provider_creates_hasher(): void
    {
        $provider = new HashServiceProvider(['driver' => 'bcrypt']);
        $provider->register();
        $hasher = $provider->makeHasher();
        $this->assertInstanceOf(HashManager::class, $hasher);
        $this->assertInstanceOf(HasherInterface::class, $hasher);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new HashServiceProvider();
        $this->assertContains(HasherInterface::class, $provider->provides());
        $this->assertContains(HashManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/hash.php';
        $this->assertArrayHasKey('driver', $config);
        $this->assertArrayHasKey('argon2id', $config);
        $this->assertArrayHasKey('bcrypt', $config);
        $this->assertArrayHasKey('rehash_on_login', $config);
        $this->assertSame(65536, $config['argon2id']['memory_cost']);
        $this->assertSame(4, $config['argon2id']['time_cost']);
        $this->assertSame(1, $config['argon2id']['threads']);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/hash.php';
        $this->assertArrayHasKey('error_hash_failed', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/hash.php';
        $this->assertArrayHasKey('error_hash_failed', $messages);
    }

    public function test_end_to_end_make_check(): void
    {
        $manager = new HashManager(['driver' => 'bcrypt']);
        $hash = $manager->make('secret123');
        $this->assertTrue($manager->check('secret123', $hash));
        $this->assertFalse($manager->check('wrong', $hash));
    }

    public function test_rehash_workflow(): void
    {
        $manager = new HashManager(['driver' => 'bcrypt', 'bcrypt' => ['cost' => 4]]);
        $oldHash = $manager->make('password');
        // After upgrading cost
        $manager2 = new HashManager(['driver' => 'bcrypt', 'bcrypt' => ['cost' => 12]]);
        $this->assertTrue($manager2->needsRehash($oldHash));
        $newHash = $manager2->make('password');
        $this->assertFalse($manager2->needsRehash($newHash));
    }
}
