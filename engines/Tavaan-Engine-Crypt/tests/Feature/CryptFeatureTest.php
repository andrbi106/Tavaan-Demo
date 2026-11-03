<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Crypt\Encrypter;
use Tavaan\Engine\Crypt\KeyRing;
use Tavaan\Engine\Crypt\CryptServiceProvider;
use Tavaan\Engine\Crypt\Contracts\EncrypterInterface;

/**
 * تست‌های Feature برای موتور Crypt.
 */
final class CryptFeatureTest extends TestCase
{
    public function test_service_provider_creates_encrypter(): void
    {
        $provider = new CryptServiceProvider([]);
        $provider->register();
        $enc = $provider->getEncrypter();
        $this->assertInstanceOf(Encrypter::class, $enc);
        $this->assertInstanceOf(EncrypterInterface::class, $enc);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new CryptServiceProvider([]);
        $this->assertContains(EncrypterInterface::class, $provider->provides());
        $this->assertContains(Encrypter::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/crypt.php';
        $this->assertArrayHasKey('key', $config);
        $this->assertArrayHasKey('key_version', $config);
        $this->assertArrayHasKey('cipher', $config);
        $this->assertSame('aes-256-gcm', $config['cipher']);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/crypt.php';
        $this->assertArrayHasKey('error_encrypt', $messages);
        $this->assertArrayHasKey('error_decrypt', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/crypt.php';
        $this->assertArrayHasKey('error_encrypt', $messages);
        $this->assertArrayHasKey('error_decrypt', $messages);
    }

    public function test_end_to_end_encryption_workflow(): void
    {
        $key = KeyRing::generateKey();
        $keyRing = new KeyRing($key);
        $enc = new Encrypter($keyRing);

        // Plain text roundtrip
        $plain = 'Sensitive data';
        $cipher = $enc->encrypt($plain);
        $this->assertSame($plain, $enc->decrypt($cipher));

        // JSON roundtrip
        $data = ['user_id' => 42, 'email' => 'user@example.com'];
        $cipher = $enc->encryptJson($data);
        $this->assertSame($data, $enc->decryptJson($cipher));

        // HMAC
        $mac = $enc->hmac('data');
        $this->assertSame(64, \strlen($mac));

        // Key derivation
        $derived = $enc->deriveKey('master', 'context');
        $this->assertSame(32, \strlen($derived));
    }

    public function test_key_rotation_workflow(): void
    {
        $key1 = KeyRing::generateKey();
        $keyRing = new KeyRing($key1, 1);
        $enc = new Encrypter($keyRing);

        // Encrypt with old key
        $oldCipher = $enc->encrypt('old_data');

        // Rotate to new key
        $key2 = KeyRing::generateKey();
        $keyRing->rotate($key2);

        // Encrypt with new key
        $newCipher = $enc->encrypt('new_data');

        // Both should decrypt (old with key v1, new with key v2)
        $this->assertSame('old_data', $enc->decrypt($oldCipher));
        $this->assertSame('new_data', $enc->decrypt($newCipher));

        // New cipher should use v2
        $parts = \explode(':', $newCipher);
        $this->assertSame('v2', $parts[0]);
    }
}
