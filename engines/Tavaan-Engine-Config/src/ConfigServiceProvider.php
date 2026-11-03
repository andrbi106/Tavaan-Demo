<?php

declare(strict_types=1);

namespace Tavaan\Engine\Config;

use Tavaan\Engine\Config\Contracts\RepositoryInterface;

/**
 * ConfigServiceProvider — ثبت سرویس‌های Config در Container.
 *
 * در عمل، Repository و EnvRepository را در Container ثبت می‌کند.
 */
final class ConfigServiceProvider
{
    /**
     * @var array<string, mixed>|null
     */
    private static ?array $items = null;

    public function __construct(
        private readonly ?string $configDir = null,
    ) {
    }

    public function register(): void
    {
        // Build the config from the loader
        if (self::$items === null) {
            if ($this->configDir !== null) {
                $loader = new ConfigLoader($this->configDir);
                self::$items = $loader->load();
            } else {
                self::$items = [];
            }
        }
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
        return [
            RepositoryInterface::class,
            Repository::class,
            EnvRepository::class,
        ];
    }

    public static function getRepository(): Repository
    {
        if (self::$items === null) {
            self::$items = [];
        }
        return new Repository(self::$items);
    }

    public static function getEnvRepository(): EnvRepository
    {
        return new EnvRepository();
    }

    /**
     * @param array<string, mixed> $items
     */
    public static function setItems(array $items): void
    {
        self::$items = $items;
    }
}
