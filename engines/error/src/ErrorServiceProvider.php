<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

/**
 * ErrorServiceProvider — ثبت موتور Error.
 */
final class ErrorServiceProvider
{
    private ?ErrorHandler $handler = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->handler = new ErrorHandler(
            debug: $this->config['debug'] ?? false,
            logger: $this->config['logger'] ?? null,
        );
    }

    public function boot(): void
    {
        if ($this->handler !== null && ($this->config['auto_register'] ?? false)) {
            $this->handler->register();
        }
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [ErrorHandler::class, HttpException::class];
    }

    public function getHandler(): ErrorHandler
    {
        if ($this->handler === null) {
            $this->register();
        }
        return $this->handler;
    }
}
