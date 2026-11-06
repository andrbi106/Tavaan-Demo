<?php
declare(strict_types=1);
namespace Tavaan\Core\Bootstrap;

use Tavaan\Core\Application\ApplicationInterface;

final class BootProviders implements BootstrapperInterface
{
    public function __construct(
        private readonly RegisterProviders $registerProviders
    ) {}

    public function bootstrap(ApplicationInterface $app): void
    {
        foreach ($this->registerProviders->registeredProviders() as $provider) {
            $provider->boot();
        }
    }
}
