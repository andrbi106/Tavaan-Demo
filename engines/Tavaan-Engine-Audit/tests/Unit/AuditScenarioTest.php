<?php

declare(strict_types=1);

namespace Tavaan\Engine\Audit\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Audit\Auditor;

/**
 * T-NN scenario tests for the audit engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class AuditScenarioTest extends TestCase
{
    public function test_t01_record_hash_chain(): void
    {
        // Scenario T-01
$a = new Auditor("secret-key");
        $id1 = $a->record(["action" => "login", "user" => "u1"]);
        $id2 = $a->record(["action" => "logout", "user" => "u1"]);
        $this->assertCount(2, $a->entries());
        $this->assertTrue($a->verify(), "hash chain should verify");
        $this->assertNotEmpty($id1);
        $this->assertNotEmpty($id2);
    }
    public function test_t02_tamper_breaks_verify(): void
    {
        // Scenario T-02
$a = new Auditor("k");
        $a->record(["amount" => 100]);
        $a->record(["amount" => 200]);
        // Mutate the first entry's data
        $entries = $a->entries();
        $this->assertCount(2, $entries);
        // Tamper via reflection
        $r = new \ReflectionClass($a);
        $prop = $r->getProperty("entries");
        $entries = $prop->getValue($a);
        $entries[0]["data"]["amount"] = 999;
        $prop->setValue($a, $entries);
        $this->assertFalse($a->verify(), "tampered chain should fail verify");
    }
    public function test_t03_export_filters(): void
    {
        // Scenario T-03
$a = new Auditor();
        $a->record(["type" => "auth", "user" => "u1"]);
        $a->record(["type" => "data", "user" => "u2"]);
        $a->record(["type" => "auth", "user" => "u3"]);
        $auth = $a->export(fn($e) => ($e["data"]["type"] ?? "") === "auth");
        $this->assertCount(2, $auth);
    }
    public function test_t04_prune_old_entries(): void
    {
        // Scenario T-04
$a = new Auditor();
        $a->record(["v" => 1]);
        $a->record(["v" => 2]);
        $deleted = $a->prune(90);
        $this->assertSame(0, $deleted, "nothing older than 90 days yet");
        $this->assertCount(2, $a->entries());
    }
    public function test_t05_last_hash_tracks_chain(): void
    {
        // Scenario T-05
$a = new Auditor("secret");
        $this->assertSame("", $a->lastHash());
        $a->record(["x" => 1]);
        $h1 = $a->lastHash();
        $this->assertNotEmpty($h1);
        $a->record(["x" => 2]);
        $h2 = $a->lastHash();
        $this->assertNotSame($h1, $h2);
    }
}
