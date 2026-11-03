<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Hash\HashManager;
use Tavaan\Engine\Hash\Argon2idHasher;
use Tavaan\Engine\Hash\BcryptHasher;
use Tavaan\Engine\Hash\Contracts\HasherInterface;

/**
 * تست‌های Unit برای موتور Hash.
 *
 * @see docs/03-engines/hash.md (DOC-055)
 */
final class HashTest extends TestCase
{
    public function test_argon2id_make_check_roundtrip(): void
    {
        $hasher = new Argon2idHasher();
        $hash = $hasher->make('my_password');
        $this->assertNotEmpty($hash);
        $this->assertTrue($hasher->check('my_password', $hash));
    }

    public function test_argon2id_check_wrong_password_returns_false(): void
    {
        $hasher = new Argon2idHasher();
        $hash = $hasher->make('correct');
        $this->assertFalse($hasher->check('wrong', $hash));
    }

    public function test_argon2id_check_empty_returns_false(): void
    {
        $hasher = new Argon2idHasher();
        $hash = $hasher->make('test');
        $this->assertFalse($hasher->check('', $hash));
        $this->assertFalse($hasher->check('test', ''));
    }

    public function test_argon2id_info(): void
    {
        $hasher = new Argon2idHasher();
        $hash = $hasher->make('test');
        $info = $hasher->info($hash);
        $this->assertArrayHasKey('algo', $info);
        $this->assertArrayHasKey('options', $info);
        $this->assertNotSame('unknown', $info['algo']);
    }

    public function test_bcrypt_make_check_roundtrip(): void
    {
        $hasher = new BcryptHasher();
        $hash = $hasher->make('my_password');
        $this->assertNotEmpty($hash);
        $this->assertTrue($hasher->check('my_password', $hash));
    }

    public function test_bcrypt_check_wrong_password_returns_false(): void
    {
        $hasher = new BcryptHasher();
        $hash = $hasher->make('correct');
        $this->assertFalse($hasher->check('wrong', $hash));
    }

    public function test_bcrypt_info(): void
    {
        $hasher = new BcryptHasher();
        $hash = $hasher->make('test');
        $info = $hasher->info($hash);
        $this->assertArrayHasKey('algo', $info);
        $this->assertNotSame('unknown', $info['algo']);
    }

    public function test_hash_manager_with_argon2id(): void
    {
        $manager = new HashManager(['driver' => 'argon2id']);
        $hash = $manager->make('test');
        $this->assertTrue($manager->check('test', $hash));
        $this->assertInstanceOf(Argon2idHasher::class, $manager->getDriver());
    }

    public function test_hash_manager_with_bcrypt(): void
    {
        $manager = new HashManager(['driver' => 'bcrypt']);
        $hash = $manager->make('test');
        $this->assertTrue($manager->check('test', $hash));
        $this->assertInstanceOf(BcryptHasher::class, $manager->getDriver());
    }

    public function test_hash_manager_default_is_argon2id(): void
    {
        $manager = new HashManager([]);
        $this->assertInstanceOf(Argon2idHasher::class, $manager->getDriver());
    }

    public function test_hash_manager_implements_interface(): void
    {
        $manager = new HashManager([]);
        $this->assertInstanceOf(HasherInterface::class, $manager);
    }

    public function test_needs_rehash_returns_false_for_fresh_hash(): void
    {
        $hasher = new BcryptHasher();
        $hash = $hasher->make('test');
        $this->assertFalse($hasher->needsRehash($hash));
    }

    public function test_needs_rehash_returns_true_for_low_cost_hash(): void
    {
        $hasher = new BcryptHasher(cost: 4);
        $hash = $hasher->make('test');
        // Needs rehash because default cost (12) != current (4)
        $this->assertTrue($hasher->needsRehash($hash));
    }

    public function test_make_with_options(): void
    {
        $hasher = new BcryptHasher();
        $hash = $hasher->make('test', ['cost' => 4]);
        $info = $hasher->info($hash);
        $this->assertSame(4, $info['options']['cost'] ?? null);
    }

    public function test_argon2id_with_custom_params(): void
    {
        $hasher = new Argon2idHasher(memoryCost: 32768, timeCost: 2, threads: 1);
        $hash = $hasher->make('test');
        $this->assertTrue($hasher->check('test', $hash));
    }

    public function test_different_hashes_for_different_passwords(): void
    {
        $hasher = new BcryptHasher();
        $h1 = $hasher->make('password1');
        $h2 = $hasher->make('password2');
        $this->assertNotSame($h1, $h2);
    }

    public function test_different_hashes_for_same_password_due_to_random_salt(): void
    {
        $hasher = new BcryptHasher();
        $h1 = $hasher->make('same_password');
        $h2 = $hasher->make('same_password');
        $this->assertNotSame($h1, $h2);
        $this->assertTrue($hasher->check('same_password', $h1));
        $this->assertTrue($hasher->check('same_password', $h2));
    }
}
