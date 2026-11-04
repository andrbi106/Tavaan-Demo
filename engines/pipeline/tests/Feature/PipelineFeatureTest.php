<?php

declare(strict_types=1);

namespace Tavaan\Engine\Pipeline\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Pipeline\Pipeline;
use Tavaan\Engine\Pipeline\PipelineServiceProvider;

/**
 * تست‌های Feature برای موتور Pipeline.
 */
final class PipelineFeatureTest extends TestCase
{
    public function test_service_provider_creates_pipeline(): void
    {
        $provider = new PipelineServiceProvider();
        $provider->register();
        $p = $provider->makePipeline();
        $this->assertInstanceOf(Pipeline::class, $p);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new PipelineServiceProvider();
        $this->assertContains(Pipeline::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/pipeline.php';
        $this->assertArrayHasKey('method', $config);
        $this->assertArrayHasKey('terminate_method', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/pipeline.php';
        $this->assertArrayHasKey('error_pipe_not_found', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/pipeline.php';
        $this->assertArrayHasKey('error_pipe_not_found', $messages);
    }

    public function test_end_to_end_pipeline_with_provider(): void
    {
        $provider = new PipelineServiceProvider();
        $provider->register();
        $result = $provider->makePipeline()
            ->send('hello')
            ->through([
                fn($p, $next) => $next(\strtoupper($p)),
                fn($p, $next) => $next($p . '!'),
            ])
            ->then(fn($p) => $p . ' world');
        $this->assertSame('HELLO! world', $result);
    }
}
