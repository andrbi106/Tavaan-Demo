<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash;

use Tavaan\Engine\Hash\Contracts\HasherInterface;

/**
 * HashServiceProvider — ثبت موتور Hash.
 */
final class HashServiceProvider
{
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        // No-op — HashManager با new HashManager($config) ساخته می‌شود
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
        return [HasherInterface::class, HashManager::class];
    }

    public function makeHasher(): HashManager
    {
        return new HashManager($this->config);
    }
}
