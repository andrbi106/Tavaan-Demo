<?php
declare(strict_types=1);
namespace Tavaan\Core\Bootstrap;

use Tavaan\Core\Application\ApplicationInterface;

interface BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void;
}
