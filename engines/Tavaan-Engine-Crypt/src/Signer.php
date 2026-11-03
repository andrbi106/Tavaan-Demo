<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt;

/**
 * Signer — پیاده‌سازی مطابق مستندات.
 *
 * @see docs/03-engines/ (YAML machine-readable block)
 */
final class Signer
{
    /**
     * sign
     */
    public function sign(string $payload, ?string $privateKey = null): string
    {
        $key = $privateKey ?? $this->keys->currentKey();
        return hash_hmac("sha256", $payload, $key);
    }
    /**
     * verify
     */
    public function verify(string $payload, string $signature, string $publicKey): bool
    {
        $expected = hash_hmac("sha256", $payload, $publicKey);
        return hash_equals($expected, $signature);
    }
    /**
     * generateKeyPair
     */
    public function generateKeyPair(): array
    {
        $config = ["digest_alg" => "sha256", "private_key_bits" => 2048, "private_key_type" => OPENSSL_KEYTYPE_RSA];
        $key = openssl_pkey_new($config);
        if ($key === false) return ["private" => "", "public" => ""];
        openssl_pkey_export($key, $private);
        $details = openssl_pkey_get_details($key);
        return ["private" => $private, "public" => $details["key"] ?? ""];
    }
}
