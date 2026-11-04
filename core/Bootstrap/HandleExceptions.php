<?php
declare(strict_types=1);
namespace Tavaan\Core\Bootstrap;

use Tavaan\Core\Application\ApplicationInterface;
use Tavaan\Core\Error\ErrorHandler;
use Tavaan\Core\Error\LogMasker;

final class HandleExceptions implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        $handler = new ErrorHandler(
            events: $app->bound('events') ? $app->make('events') : null,
            masker: new LogMasker(),
        );
        $handler->register();
    }
}
