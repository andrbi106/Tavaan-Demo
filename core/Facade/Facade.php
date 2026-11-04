<?php

declare(strict_types=1);

/**
 * تَوان | Tavaan — Facade (کلاس پایه)
 *
 * Facade دسترسی استاتیک به سرویس‌های کانتینر را فراهم می‌کند.
 * همهٔ Facadeهای موتورها از این کلاس ارث می‌برند.
 *
 * مثال:
 *   class Auth extends Facade {
 *       protected static function getFacadeAccessor(): string { return 'auth'; }
 *   }
 *   // استفاده:
 *   Auth::check();  // ← delegates to container->make('auth')->check()
 */

namespace Tavaan\Core\Facade;

use Tavaan\Core\Contracts\Container\ContainerInterface;

abstract class Facade
{
    protected static ?ContainerInterface $container = null;

    /** تنظیم کانتینر برای Facadeها. */
    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    /** شناسهٔ سرویس در کانتینر — زیرکلاس‌ها باید override کنند. */
    abstract protected static function getFacadeAccessor(): string;

    /** Resolve سرویس از کانتینر. */
    protected static function resolveFacadeAccessor(): mixed
    {
        if (self::$container === null) {
            throw new \RuntimeException(
                'Facade container not set. Call Facade::setContainer() first.'
            );
        }
        return self::$container->make(static::getFacadeAccessor());
    }

    /**
     * دسترسی استاتیک به متدهای سرویس.
     *
     * @param string $method
     * @param list<mixed> $args
     * @return mixed
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = static::resolveFacadeAccessor();
        return $instance->$method(...$args);
    }
}
