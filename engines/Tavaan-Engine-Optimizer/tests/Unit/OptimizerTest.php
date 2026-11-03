<?php

declare(strict_types=1);

namespace Tavaan\Engine\Optimizer\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Optimizer\Optimizer;
use Tavaan\Engine\Optimizer\OptimizerResult;
use Tavaan\Engine\Optimizer\OptimizerServiceProvider;
use Tavaan\Engine\Optimizer\ConfigCacher;
use Tavaan\Engine\Optimizer\RouteCacher;
use Tavaan\Engine\Optimizer\ViewCacher;
use Tavaan\Engine\Optimizer\AutoloadCacher;
use Tavaan\Engine\Optimizer\EventCacher;
use Tavaan\Engine\Optimizer\Cacher;

final class OptimizerTest extends TestCase
{
    private function tmpDir(): string
    {
        $dir = \sys_get_temp_dir() . '/tvn_opt_' . \uniqid();
        \mkdir($dir, 0777, true);
        return $dir;
    }

    public function test_config_cacher_writes_file(): void
    {
        $path = $this->tmpDir() . '/config.php';
        $c = new ConfigCacher($path, ['app' => ['name' => 'Tavaan']]);
        $this->assertTrue($c->cache());
        $this->assertTrue($c->isCached());
        $this->assertSame($path, $c->getCachedPath());
        $data = require $path;
        $this->assertSame(['app' => ['name' => 'Tavaan']], $data);
    }

    public function test_config_cacher_clear(): void
    {
        $path = $this->tmpDir() . '/config.php';
        $c = new ConfigCacher($path, ['x' => 1]);
        $c->cache();
        $this->assertTrue($c->isCached());
        $c->clear();
        $this->assertFalse($c->isCached());
    }

    public function test_route_cacher(): void
    {
        $path = $this->tmpDir() . '/routes.php';
        $c = new RouteCacher($path, ['/users' => 'UserController@index']);
        $this->assertTrue($c->cache());
        $this->assertTrue($c->isCached());
    }

    public function test_view_cacher(): void
    {
        $path = $this->tmpDir() . '/views.php';
        $c = new ViewCacher($path, ['welcome' => 'compiled code']);
        $this->assertTrue($c->cache());
    }

    public function test_autoload_cacher(): void
    {
        $path = $this->tmpDir() . '/classmap.php';
        $c = new AutoloadCacher($path, ['App\\User' => 'app/User.php']);
        $this->assertTrue($c->cache());
        $data = require $path;
        $this->assertSame(['App\\User' => 'app/User.php'], $data);
    }

    public function test_event_cacher(): void
    {
        $path = $this->tmpDir() . '/events.php';
        $c = new EventCacher($path, ['user.created' => ['SendEmail']]);
        $this->assertTrue($c->cache());
    }

    public function test_optimizer_optimize_all(): void
    {
        $dir = $this->tmpDir();
        $opt = new Optimizer([
            'cache_dir' => $dir,
            'config_data' => ['app' => 'test'],
            'routes_data' => ['/users' => 'ctrl'],
        ]);
        $result = $opt->optimize();
        $this->assertInstanceOf(OptimizerResult::class, $result);
        $this->assertArrayHasKey('config', $result->times);
        $this->assertArrayHasKey('routes', $result->times);
        $this->assertGreaterThan(0, $result->totalMs);
    }

    public function test_optimizer_is_cached_after_optimize(): void
    {
        $dir = $this->tmpDir();
        $opt = new Optimizer(['cache_dir' => $dir, 'config_data' => ['x' => 1]]);
        $this->assertFalse($opt->isCached('config'));
        $opt->optimize();
        $this->assertTrue($opt->isCached('config'));
    }

    public function test_optimizer_clear(): void
    {
        $dir = $this->tmpDir();
        $opt = new Optimizer(['cache_dir' => $dir, 'config_data' => ['x' => 1]]);
        $opt->optimize();
        $this->assertTrue($opt->isCached('config'));
        $opt->clear();
        $this->assertFalse($opt->isCached('config'));
    }

    public function test_optimizer_clear_one(): void
    {
        $dir = $this->tmpDir();
        $opt = new Optimizer(['cache_dir' => $dir, 'config_data' => ['x' => 1], 'routes_data' => []]);
        $opt->optimize();
        $this->assertTrue($opt->isCached('config'));
        $this->assertTrue($opt->isCached('routes'));
        $opt->clearOne('config');
        $this->assertFalse($opt->isCached('config'));
        $this->assertTrue($opt->isCached('routes'));
    }

    public function test_optimizer_get_cacher(): void
    {
        $opt = new Optimizer(['cache_dir' => $this->tmpDir()]);
        $this->assertInstanceOf(Cacher::class, $opt->getCacher('config'));
        $this->assertNull($opt->getCacher('nonexistent'));
    }

    public function test_optimizer_all(): void
    {
        $opt = new Optimizer(['cache_dir' => $this->tmpDir()]);
        $cachers = $opt->all();
        $this->assertArrayHasKey('config', $cachers);
        $this->assertArrayHasKey('routes', $cachers);
        $this->assertArrayHasKey('views', $cachers);
        $this->assertArrayHasKey('autoload', $cachers);
        $this->assertArrayHasKey('events', $cachers);
    }

    public function test_result_summary(): void
    {
        $result = new OptimizerResult(['config' => 1.5, 'routes' => 0.5], 2.0);
        $summary = $result->summary();
        $this->assertStringContainsString('config: 1.50 ms', $summary);
        $this->assertStringContainsString('routes: 0.50 ms', $summary);
        $this->assertStringContainsString('Total: 2.00 ms', $summary);
    }

    public function test_service_provider(): void
    {
        $provider = new OptimizerServiceProvider(['cache_dir' => $this->tmpDir()]);
        $provider->register();
        $this->assertInstanceOf(Optimizer::class, $provider->getOptimizer());
        $this->assertContains(Optimizer::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/optimizer.php';
        $this->assertArrayHasKey('cache_dir', $config);
    }

    public function test_cacher_interface_implemented(): void
    {
        $path = $this->tmpDir() . '/c.php';
        $this->assertInstanceOf(Cacher::class, new ConfigCacher($path, []));
        $this->assertInstanceOf(Cacher::class, new RouteCacher($path, []));
        $this->assertInstanceOf(Cacher::class, new ViewCacher($path, []));
        $this->assertInstanceOf(Cacher::class, new AutoloadCacher($path, []));
        $this->assertInstanceOf(Cacher::class, new EventCacher($path, []));
    }
}
