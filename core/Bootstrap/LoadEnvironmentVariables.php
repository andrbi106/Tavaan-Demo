<?php
declare(strict_types=1);
namespace Tavaan\Core\Bootstrap;

use Tavaan\Core\Application\ApplicationInterface;

final class LoadEnvironmentVariables implements BootstrapperInterface
{
    public function bootstrap(ApplicationInterface $app): void
    {
        $envFile = $app->basePath() . '/.env';
        if (!is_file($envFile) || !is_readable($envFile)) {
            return;
        }
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && $value[0] === substr($value, -1)) {
                $value = substr($value, 1, -1);
            }
            if (!isset($_SERVER[$key]) && !isset($_ENV[$key])) {
                $_SERVER[$key] = $value;
                $_ENV[$key] = $value;
            }
        }
    }
}
