<?php
declare(strict_types=1);
namespace Tavaan\Core\Facade\Facades;

use Tavaan\Core\Facade\Facade;

/** Facade برای Application. */
final class App extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'app';
    }
}
