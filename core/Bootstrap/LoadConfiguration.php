<?php
declare(strict_types=1);
namespace Tavaan\Core\Bootstrap;

use Tavaan\Core\Application\ApplicationInterface;

final class LoadConfiguration implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        $configDir = $app->basePath() . '/config';
        $config = [];
        if (is_dir($configDir)) {
            foreach (glob($configDir . '/*.php') ?: [] as $file) {
                $key = basename($file, '.php');
                $data = require $file;
                if (is_array($data)) {
                    $config[$key] = $data;
                }
            }
        }
        $app->instance('config', $config);
    }
}
