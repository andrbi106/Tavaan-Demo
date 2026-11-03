<?php

declare(strict_types=1);

namespace Tavaan\Engine\Hash\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Hash\HashManager;
use Tavaan\Engine\Hash\Argon2idHasher;
use Tavaan\Engine\Hash\BcryptHasher;

/**
 * T-NN scenario tests for the hash engine (real implementations).
 */
final class HashScenarioTest extends TestCase
{
    public function test_t01_make_check_roundtrip(): void
    {
        // Scenario T-01
$h = new Argon2idHasher();
        $hash = $h->make("secret");
        $this->assertTrue($h->check("secret", $hash));
        $this->assertFalse($h->check("wrong", $hash));
    }

    public function test_t02_bcrypt_make_check(): void
    {
        // Scenario T-02
$h = new BcryptHasher(cost: 4);
        $hash = $h->make("pw");
        $this->assertTrue($h->check("pw", $hash));
    }

    public function test_t03_needs_rehash_low_cost(): void
    {
        // Scenario T-03
$h = new BcryptHasher(cost: 4);
        $hash = $h->make("pw");
        $this->assertTrue($h->needsRehash($hash));
    }
}
