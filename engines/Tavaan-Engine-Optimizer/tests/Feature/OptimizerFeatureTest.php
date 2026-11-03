<?php

declare(strict_types=1);

namespace Tavaan\Engine\Optimizer\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Optimizer\Optimizer;
use Tavaan\Engine\Optimizer\OptimizerServiceProvider;

final class OptimizerFeatureTest extends TestCase
{
    public function test_end_to_end_optimize_and_clear(): void
    {
        $dir = \sys_get_temp_dir() . '/tvn_opt_' . \uniqid();
        \mkdir($dir, 0777, true);
        $provider = new OptimizerServiceProvider([
            'cache_dir' => $dir,
            'config_data' => ['app' => 'Tavaan'],
            'routes_data' => ['/' => 'HomeController@index'],
        ]);
        $provider->register();
        $opt = $provider->getOptimizer();

        $result = $opt->optimize();
        $this->assertTrue($opt->isCached('config'));
        $this->assertTrue($opt->isCached('routes'));
        $this->assertGreaterThan(0, $result->totalMs);

        $opt->clear();
        $this->assertFalse($opt->isCached('config'));
        $this->assertFalse($opt->isCached('routes'));
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/optimizer.php';
        $this->assertArrayHasKey('cache_dir', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/optimizer.php';
        $this->assertArrayHasKey('error_cache', $m);
    }
}
