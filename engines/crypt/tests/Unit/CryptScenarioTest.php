<?php

declare(strict_types=1);

namespace Tavaan\Engine\Crypt\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Crypt\Signer;
use Tavaan\Engine\Crypt\BlindIndex;

/**
 * T-NN scenario tests for the crypt engine (real implementations).
 */
final class CryptScenarioTest extends TestCase
{
    public function test_t01_blind_index_compute(): void
    {
        // Scenario T-01
$bi = new BlindIndex();
        $idx = $bi->compute("email@example.com", "key123");
        $this->assertNotEmpty($idx);
    }

    public function test_t02_blind_index_verify(): void
    {
        // Scenario T-02
$bi = new BlindIndex();
        $idx = $bi->compute("email@example.com", "key");
        $this->assertTrue($bi->verify("email@example.com", $idx, "key"));
        $this->assertFalse($bi->verify("other@example.com", $idx, "key"));
    }
}
