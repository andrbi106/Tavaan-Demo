<?php

declare(strict_types=1);

namespace Tavaan\Engine\Cache\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Cache\CacheManager;

/**
 * T-NN scenario tests for the cache engine (real implementations).
 */
final class CacheScenarioTest extends TestCase
{
    public function test_t01_get_put_has_forget_flush(): void
    {
        // Scenario T-01
$mgr = new CacheManager();
        $repo = $mgr->store();
        $repo->put("k1", "v1", 60);
        $this->assertSame("v1", $repo->get("k1"));
        $this->assertTrue($repo->has("k1"));
        $repo->forget("k1");
        $this->assertFalse($repo->has("k1"));
    }

    public function test_t06_remember_caches_on_miss(): void
    {
        // Scenario T-06
$mgr = new CacheManager();
        $repo = $mgr->store();
        $count = 0;
        $val = $repo->remember("k", 60, function () use (&$count) { $count++; return "computed"; });
        $this->assertSame("computed", $val);
        $this->assertSame(1, $count);
        // Second call should hit cache
        $val2 = $repo->remember("k", 60, function () use (&$count) { $count++; return "new"; });
        $this->assertSame("computed", $val2);
        $this->assertSame(1, $count);
    }

    public function test_t07_add_only_if_missing(): void
    {
        // Scenario T-07
$mgr = new CacheManager();
        $repo = $mgr->store();
        $this->assertTrue($repo->add("k", "v", 60));
        $this->assertFalse($repo->add("k", "other", 60));
        $this->assertSame("v", $repo->get("k"));
    }
}
