<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Contracts;

/**
 * قرارداد Encrypter.
 *
 * @see \Tavaan\Engine\Crypt\Encrypter
 * @api 1.0
 */
interface EncrypterInterface
{
    public function encrypt(string $plaintext, string $aad = ''): string;

    public function decrypt(string $payload, string $aad = ''): string;

    public function encryptString(string $value): string;

    public function decryptString(string $payload): string;

    /**
     * @param array<string, mixed> $data
     */
    public function encryptJson(array $data, string $aad = ''): string;

    /**
     * @return array<string, mixed>
     */
    public function decryptJson(string $payload, string $aad = ''): array;

    public function hmac(string $data, string $context = ''): string;

    public function deriveKey(string $ikm, string $info, int $length = 32, string $salt = ''): string;

    public function sign(string $payload, ?string $privateKey = null): string;

    public function verify(string $payload, string $signature, string $publicKey): bool;

    public function generateKeyPair(): array;
}
