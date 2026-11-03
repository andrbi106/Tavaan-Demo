<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Crypt\Encrypter;
use Tavaan\Engine\Crypt\KeyRing;
use Tavaan\Engine\Crypt\Exceptions\DecryptException;
use Tavaan\Engine\Crypt\Exceptions\EncryptionException;
use Tavaan\Engine\Crypt\Contracts\EncrypterInterface;

/**
 * تست‌های Unit برای موتور Crypt.
 *
 * @see docs/03-engines/crypt.md (DOC-054)
 */
final class CryptTest extends TestCase
{
    private function createEncrypter(): Encrypter
    {
        $key = KeyRing::generateKey();
        $keyRing = new KeyRing($key);
        return new Encrypter($keyRing);
    }

    public function test_encrypt_decrypt_roundtrip(): void
    {
        $enc = $this->createEncrypter();
        $cipher = $enc->encrypt('متن محرمانه');
        $this->assertNotSame('متن محرمانه', $cipher);
        $this->assertSame('متن محرمانه', $enc->decrypt($cipher));
    }

    public function test_encrypt_produces_v2_format(): void
    {
        $enc = $this->createEncrypter();
        $cipher = $enc->encrypt('test');
        $this->assertStringStartsWith('v1:', $cipher);
        // 4 parts
        $parts = \explode(':', $cipher);
        $this->assertCount(4, $parts);
    }

    public function test_encrypt_with_aad_roundtrip(): void
    {
        $enc = $this->createEncrypter();
        $cipher = $enc->encrypt('data', 'aad_context');
        $plain = $enc->decrypt($cipher, 'aad_context');
        $this->assertSame('data', $plain);
    }

    public function test_decrypt_with_wrong_aad_throws(): void
    {
        $enc = $this->createEncrypter();
        $cipher = $enc->encrypt('data', 'correct_aad');
        $this->expectException(DecryptException::class);
        $enc->decrypt($cipher, 'wrong_aad');
    }

    public function test_encrypt_string_decrypt_string(): void
    {
        $enc = $this->createEncrypter();
        $cipher = $enc->encryptString('hello');
        $this->assertSame('hello', $enc->decryptString($cipher));
    }

    public function test_encrypt_json_decrypt_json(): void
    {
        $enc = $this->createEncrypter();
        $data = ['name' => 'Tavaan', 'version' => 1, 'tags' => ['a', 'b']];
        $cipher = $enc->encryptJson($data);
        $decrypted = $enc->decryptJson($cipher);
        $this->assertSame($data, $decrypted);
    }

    public function test_decrypt_invalid_format_throws(): void
    {
        $enc = $this->createEncrypter();
        $this->expectException(DecryptException::class);
        $enc->decrypt('not_a_valid_format');
    }

    public function test_decrypt_tampered_data_throws(): void
    {
        $enc = $this->createEncrypter();
        $cipher = $enc->encrypt('secret');
        $parts = \explode(':', $cipher);
        $parts[2] = \base64_encode('tampered_data');
        $this->expectException(DecryptException::class);
        $enc->decrypt(\implode(':', $parts));
    }

    public function test_decrypt_with_missing_key_version_throws(): void
    {
        $key = KeyRing::generateKey();
        $keyRing = new KeyRing($key, 1);
        $enc = new Encrypter($keyRing);
        $cipher = $enc->encrypt('test');
        // Manually change version to 99
        $parts = \explode(':', $cipher);
        $parts[0] = 'v99';
        $this->expectException(DecryptException::class);
        $enc->decrypt(\implode(':', $parts));
    }

    public function test_hmac(): void
    {
        $enc = $this->createEncrypter();
        $mac1 = $enc->hmac('data');
        $mac2 = $enc->hmac('data');
        $this->assertSame($mac1, $mac2);  // deterministic
        $this->assertNotSame($mac1, $enc->hmac('different_data'));
        $this->assertSame(64, \strlen($mac1));  // SHA-256 hex
    }

    public function test_hmac_with_context(): void
    {
        $enc = $this->createEncrypter();
        $mac1 = $enc->hmac('data', 'ctx1');
        $mac2 = $enc->hmac('data', 'ctx2');
        $this->assertNotSame($mac1, $mac2);
    }

    public function test_derive_key(): void
    {
        $enc = $this->createEncrypter();
        $key1 = $enc->deriveKey('master_key', 'context1', 32);
        $key2 = $enc->deriveKey('master_key', 'context2', 32);
        $this->assertSame(32, \strlen($key1));
        $this->assertNotSame($key1, $key2);
    }

    public function test_derive_key_with_explicit_salt(): void
    {
        $enc = $this->createEncrypter();
        $key1 = $enc->deriveKey('ikm', 'info', 64, 'salt1');
        $key2 = $enc->deriveKey('ikm', 'info', 64, 'salt2');
        $this->assertSame(64, \strlen($key1));
        $this->assertNotSame($key1, $key2);
    }

    public function test_sign_verify(): void
    {
        $enc = $this->createEncrypter();
        $sig = $enc->sign('payload');
        $this->assertTrue($enc->verify('payload', $sig, KeyRing::generateKey()));  // wrong key
        $this->assertFalse($enc->verify('payload', $sig, 'wrong_key'));
    }

    public function test_generate_key_pair(): void
    {
        $enc = $this->createEncrypter();
        $keys = $enc->generateKeyPair();
        $this->assertArrayHasKey('private', $keys);
        $this->assertArrayHasKey('public', $keys);
        $this->assertStringContainsString('-----BEGIN', $keys['private']);
        $this->assertStringContainsString('-----BEGIN', $keys['public']);
    }

    public function test_rsa_sign_verify(): void
    {
        $enc = $this->createEncrypter();
        $keys = $enc->generateKeyPair();
        $sig = $enc->signWithRsa('payload', $keys['private']);
        $this->assertTrue($enc->verifyRsa('payload', $sig, $keys['public']));
        $this->assertFalse($enc->verifyRsa('tampered', $sig, $keys['public']));
    }

    public function test_keyring_basic(): void
    {
        $key = KeyRing::generateKey();
        $kr = new KeyRing($key, 1);
        $this->assertSame(1, $kr->currentVersion());
        $this->assertSame($key, $kr->currentKey());
        $this->assertSame($key, $kr->key(1));
        $this->assertNull($kr->key(99));
    }

    public function test_keyring_rotate(): void
    {
        $key1 = KeyRing::generateKey();
        $kr = new KeyRing($key1, 1);
        $key2 = KeyRing::generateKey();
        $kr->rotate($key2);
        $this->assertSame(2, $kr->currentVersion());
        $this->assertSame($key2, $kr->currentKey());
        $this->assertSame($key1, $kr->key(1));  // old key still exists
    }

    public function test_keyring_add_key(): void
    {
        $kr = new KeyRing(KeyRing::generateKey(), 1);
        $oldKey = KeyRing::generateKey();
        $kr->addKey(0, $oldKey);
        $this->assertSame($oldKey, $kr->key(0));
    }

    public function test_keyring_invalid_key_length(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new KeyRing('too_short', 1);
    }

    public function test_keyring_generate_key(): void
    {
        $key = KeyRing::generateKey();
        $this->assertSame(32, \strlen($key));
    }

    public function test_encrypter_implements_interface(): void
    {
        $enc = $this->createEncrypter();
        $this->assertInstanceOf(EncrypterInterface::class, $enc);
    }

    public function test_keyring_all_keys(): void
    {
        $k1 = KeyRing::generateKey();
        $k2 = KeyRing::generateKey();
        $kr = new KeyRing($k1, 1);
        $kr->addKey(2, $k2);
        $all = $kr->allKeys();
        $this->assertCount(2, $all);
        $this->assertSame($k1, $all[1]);
        $this->assertSame($k2, $all[2]);
    }
}
