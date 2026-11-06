<?php
declare(strict_types=1);
namespace Tavaan\Core\Application;

use Tavaan\Core\Contracts\Container\ContainerInterface;

interface ApplicationInterface extends ContainerInterface
{
    public function boot(): self;
    public function shutdown(): void;
    public function isBooted(): bool;
    public function setMode(RuntimeMode $mode): self;
    public function mode(): RuntimeMode;
    public function basePath(): string;
    public function version(): string;
}
