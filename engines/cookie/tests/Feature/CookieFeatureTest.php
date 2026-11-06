<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Cookie\Tests\Feature;

use Tavaan\Testing\TestCase;

final class CookieFeatureTest extends TestCase
{
    public function test_config_loads(): void
    {
        $config = require __DIR__ . '/../../config/cookie.php';
        $this->assertIsArray($config);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/cookie.php';
        $this->assertIsArray($m);
    }
}
