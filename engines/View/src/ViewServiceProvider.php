<?php

declare(strict_types=1);

namespace Tavaan\Engine\View;

/**
 * ViewServiceProvider — ثبت موتور View.
 */
final class ViewServiceProvider
{
    private ?ViewFactory $factory = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->factory = new ViewFactory(
            viewPath: $this->config['path'] ?? 'views',
            compiledPath: $this->config['compiled'] ?? null,
        );
    }

    public function boot(): void {}

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [ViewFactory::class, View::class];
    }

    public function getFactory(): ViewFactory
    {
        if ($this->factory === null) $this->register();
        return $this->factory;
    }
}
