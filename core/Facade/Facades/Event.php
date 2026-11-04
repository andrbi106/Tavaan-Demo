<?php
declare(strict_types=1);
namespace Tavaan\Core\Facade\Facades;

use Tavaan\Core\Facade\Facade;

/** Facade برای Event Dispatcher. */
final class Event extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'events';
    }
}
