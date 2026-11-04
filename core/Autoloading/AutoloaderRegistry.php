<?php
declare(strict_types=1);
namespace Tavaan\Core\Autoloading;

/** Registry برای دسترسی global به autoloader ثبت‌شده. */
final class AutoloaderRegistry
{
    private static ?Autoloader $instance = null;

    public static function set(Autoloader $autoloader): void
    {
        self::$instance = $autoloader;
    }

    public static function get(): Autoloader
    {
        if (self::$instance === null) {
            throw new \RuntimeException('No autoloader registered. Call AutoloaderRegistry::set() first.');
        }
        return self::$instance;
    }

    public static function has(): bool
    {
        return self::$instance !== null;
    }
}
