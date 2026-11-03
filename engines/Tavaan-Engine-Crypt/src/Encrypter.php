<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt;

use Tavaan\Engine\Crypt\Contracts\EncrypterInterface;
use Tavaan\Engine\Crypt\Exceptions\EncryptionException;
use Tavaan\Engine\Crypt\Exceptions\DecryptException;

/**
 * Encrypter — رمزنگاری AES-256-GCM با Key Versioning، HKDF، HMAC، امضا.
 *
 * 🔒 از openssl داخلی PHP استفاده می‌کند (بدون libsodium اجباری).
 * 🔒 GCM IV = 12 bytes، تصادفی برای هر رمزنگاری.
 * 🔒 Tag = 16 bytes.
 *
 * @see docs/03-engines/crypt.md (DOC-054)
 */
final class Encrypter implements EncrypterInterface
{
    public function __construct(
        private readonly KeyRing $keys,
        private readonly string $cipher = 'aes-256-gcm',
    ) {
    }

    public function encrypt(string $plaintext, string $aad = ''): string
    {
        $version = $this->keys->currentVersion();
        $key = $this->keys->currentKey();
        $iv = \random_bytes(12);
        $tag = '';
        $ciphertext = \openssl_encrypt(
            $plaintext,
            $this->cipher,
            $key,
            \OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad,
            16,
        );
        if ($ciphertext === false) {
            throw EncryptionException::forOpenSslError('Encrypt failed');
        }
        return \sprintf('v%d:%s:%s:%s', $version,
            \base64_encode($iv),
            \base64_encode($ciphertext),
            \base64_encode($tag));
    }

    public function decrypt(string $payload, string $aad = ''): string
    {
        $parts = \explode(':', $payload);
        if (\count($parts) !== 4 || !\str_starts_with($parts[0], 'v')) {
            throw DecryptException::invalidFormat();
        }
        $version = (int) \substr($parts[0], 1);
        $key = $this->keys->key($version);
        if ($key === null) {
            throw DecryptException::keyMissing($version);
        }
        $iv = \base64_decode($parts[1], true);
        $ct = \base64_decode($parts[2], true);
        $tag = \base64_decode($parts[3], true);
        if ($iv === false || $ct === false || $tag === false || \strlen($tag) !== 16) {
            throw DecryptException::invalidPayload();
        }
        $plain = \openssl_decrypt($ct, $this->cipher, $key, \OPENSSL_RAW_DATA, $iv, $tag, $aad);
        if ($plain === false) {
            throw DecryptException::decryptFailed();
        }
        return $plain;
    }

    public function encryptString(string $value): string
    {
        return $this->encrypt($value);
    }

    public function decryptString(string $payload): string
    {
        return $this->decrypt($payload);
    }

    public function encryptJson(array $data, string $aad = ''): string
    {
        $json = \json_encode($data, \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);
        return $this->encrypt($json, $aad);
    }

    public function decryptJson(string $payload, string $aad = ''): array
    {
        $plain = $this->decrypt($payload, $aad);
        $data = \json_decode($plain, true);
        if (!\is_array($data)) {
            throw DecryptException::invalidJson();
        }
        return $data;
    }

    public function hmac(string $data, string $context = ''): string
    {
        $key = $this->keys->currentKey();
        // Mix context into key via HKDF
        if ($context !== '') {
            $key = $this->deriveKey($key, $context, 32);
        }
        return \hash_hmac('sha256', $data, $key);
    }

    public function deriveKey(string $ikm, string $info, int $length = 32, string $salt = ''): string
    {
        if (\function_exists('hash_hkdf')) {
            return \hash_hkdf('sha256', $ikm, $length, $info, $salt);
        }
        // Manual HKDF (RFC 5869)
        $salt = $salt !== '' ? $salt : \str_repeat("\0", 32);
        $prk = \hash_hmac('sha256', $ikm, $salt, true);
        $okm = '';
        $t = '';
        $i = 1;
        while (\strlen($okm) < $length) {
            $t = \hash_hmac('sha256', $t . $info . \chr($i), $prk, true);
            $okm .= $t;
            $i++;
        }
        return \substr($okm, 0, $length);
    }

    public function sign(string $payload, ?string $privateKey = null): string
    {
        if ($privateKey === null) {
            $privateKey = $this->keys->currentKey();
        }
        // Use Ed25519 if sodium is available, otherwise HMAC-SHA256
        if (\function_exists('sodium_crypto_sign_detached')) {
            // Need sodium keypair — for simplicity, use HMAC if no keypair is provided
            return \hash_hmac('sha256', $payload, $privateKey);
        }
        return \hash_hmac('sha256', $payload, $privateKey);
    }

    public function verify(string $payload, string $signature, string $publicKey): bool
    {
        // For HMAC-based signatures, publicKey is the secret
        $expected = \hash_hmac('sha256', $payload, $publicKey);
        return \hash_equals($expected, $signature);
    }

    /**
     * تولید جفت کلید RSA برای امضای دیجیتال (اختیاری).
     *
     * @return array{private: string, public: string}
     */
    public function generateKeyPair(): array
    {
        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => \OPENSSL_KEYTYPE_RSA,
        ];
        $key = \openssl_pkey_new($config);
        if ($key === false) {
            throw EncryptionException::forOpenSslError('Failed to generate key pair');
        }
        \openssl_pkey_export($key, $private);
        $details = \openssl_pkey_get_details($key);
        $public = $details['key'] ?? '';
        return ['private' => $private, 'public' => $public];
    }

    /**
     * امضای دیجیتال با کلید خصوصی RSA.
     */
    public function signWithRsa(string $payload, string $privateKeyPem): string
    {
        $key = \openssl_pkey_get_private($privateKeyPem);
        if ($key === false) {
            throw EncryptionException::forOpenSslError('Invalid private key');
        }
        $signature = '';
        $ok = \openssl_sign($payload, $signature, $key, \OPENSSL_ALGO_SHA256);
        if (!$ok) {
            throw EncryptionException::forOpenSslError('Sign failed');
        }
        return \base64_encode($signature);
    }

    /**
     * تأیید امضای دیجیتال RSA.
     */
    public function verifyRsa(string $payload, string $signatureB64, string $publicKeyPem): bool
    {
        $key = \openssl_pkey_get_public($publicKeyPem);
        if ($key === false) {
            return false;
        }
        $signature = \base64_decode($signatureB64, true);
        if ($signature === false) {
            return false;
        }
        return \openssl_verify($payload, $signature, $key, \OPENSSL_ALGO_SHA256) === 1;
    }
}
