<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Routing\Tests\Feature;

use Tavaan\Testing\TestCase;

final class RouterFeatureTest extends TestCase
{
    public function test_config_loads(): void
    {
        $config = require __DIR__ . '/../../config/router.php';
        $this->assertIsArray($config);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/router.php';
        $this->assertIsArray($m);
    }
}
