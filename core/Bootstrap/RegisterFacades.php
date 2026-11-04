<?php
declare(strict_types=1);
namespace Tavaan\Core\Bootstrap;

use Tavaan\Core\Application\ApplicationInterface;
use Tavaan\Core\Facade\Facade;

final class RegisterFacades implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        Facade::setContainer($app);
    }
}
