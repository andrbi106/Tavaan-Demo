<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation;

/**
 * ValidationServiceProvider — ثبت موتور Validation.
 */
final class ValidationServiceProvider
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void {}

    public function boot(): void {}

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [Validator::class];
    }
}
