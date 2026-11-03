<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt;

use Tavaan\Engine\Crypt\Contracts\EncrypterInterface;

/**
 * CryptServiceProvider — ثبت موتور Crypt.
 */
final class CryptServiceProvider
{
    private ?Encrypter $encrypter = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        // Build KeyRing from config
        $key = $this->config['key'] ?? '';
        $version = $this->config['key_version'] ?? 1;
        if (\strlen($key) !== 32) {
            // Use a random key (e.g., for tests)
            $key = \random_bytes(32);
        }
        $keyRing = new KeyRing($key, $version);
        $this->encrypter = new Encrypter($keyRing, $this->config['cipher'] ?? 'aes-256-gcm');
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [EncrypterInterface::class, Encrypter::class];
    }

    public function getEncrypter(): Encrypter
    {
        if ($this->encrypter === null) {
            $this->register();
        }
        return $this->encrypter;
    }
}
