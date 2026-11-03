<?php

declare(strict_types=1);

namespace Tavaan\Engine\Pipeline;

/**
 * PipelineServiceProvider — ثبت موتور Pipeline.
 *
 * Pipeline با نوع‌های مختلف Container سازگار است (هر آبجکتی که متد make() داشته باشد).
 */
final class PipelineServiceProvider
{
    public function __construct(
        private readonly ?object $container = null,
    ) {
    }

    public function register(): void
    {
        // No-op — Pipeline با new Pipeline() ساخته می‌شود
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [Pipeline::class];
    }

    public function makePipeline(): Pipeline
    {
        return new Pipeline($this->container);
    }
}
