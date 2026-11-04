<?php
declare(strict_types=1);
namespace Tavaan\Core\Application;

use Tavaan\Core\Container\Container;
use Tavaan\Core\Contracts\Events\DispatcherInterface;
use Tavaan\Core\Exceptions\Core\KernelException;

/**
 * اپلیکیشن تَوان — مدیریت چرخهٔ عمر فریمورک.
 * Application خودش یک Container است (می‌تواند bindingهای خود را داشته باشد).
 */
final class Application extends Container implements ApplicationInterface
{
    private bool $booted = false;
    private RuntimeMode $mode = RuntimeMode::Production;

    public function __construct(
        private readonly string $basePath,
        private readonly string $version = '0.22.0',
        private readonly ?DispatcherInterface $events = null,
    ) {
        parent::__construct();
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
        $this->instance(ApplicationInterface::class, $this);
    }

    public function boot(): self
    {
        if ($this->booted) {
            throw KernelException::forAlreadyBooted();
        }
        $this->booted = true;
        return $this;
    }

    public function shutdown(): void
    {
        $this->booted = false;
        $this->flush();
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function setMode(RuntimeMode $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function mode(): RuntimeMode
    {
        return $this->mode;
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function version(): string
    {
        return $this->version;
    }
}
