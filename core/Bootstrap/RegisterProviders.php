<?php
declare(strict_types=1);
namespace Tavaan\Core\Bootstrap;

use Tavaan\Core\Application\ApplicationInterface;
use Tavaan\Core\Provider\ServiceProvider;

final class RegisterProviders implements BootstrapperInterface
{
    /** @var list<ServiceProvider> */
    private array $registered = [];

    public function bootstrap(ApplicationInterface $app): void
    {
        $providersFile = $app->basePath() . '/config/providers.php';
        if (!is_file($providersFile)) {
            return;
        }
        /** @var list<class-string> $providers */
        $providers = require $providersFile;
        if (!is_array($providers)) {
            return;
        }
        foreach ($providers as $providerClass) {
            if (!class_exists($providerClass)) {
                continue;
            }
            $provider = new $providerClass($app);
            $provider->register();
            $this->registered[] = $provider;
            $app->instance($providerClass, $provider);
        }
    }

    /** @return list<ServiceProvider> */
    public function registeredProviders(): array
    {
        return $this->registered;
    }
}
