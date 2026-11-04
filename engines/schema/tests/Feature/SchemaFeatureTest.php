<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\ConnectionManager;
use Tavaan\Engine\Database\Schema\Blueprint;
use Tavaan\Engine\Database\Schema\SchemaBuilder;
use Tavaan\Engine\Database\Schema\SchemaManager;
use Tavaan\Engine\Database\Schema\SchemaServiceProvider;

final class SchemaFeatureTest extends TestCase
{
    public function test_end_to_end_create_and_alter(): void
    {
        $cm = new ConnectionManager([
            'default' => 'default',
            'connections' => ['default' => ['driver' => 'sqlite', 'database' => ':memory:']],
        ]);
        $provider = new SchemaServiceProvider([]);
        $provider->setConnectionManager($cm);
        $provider->register();
        $m = $provider->getManager();

        // Create
        $m->create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->timestamps();
        });
        $this->assertTrue($m->hasTable('users'));

        // Alter
        $m->table('users', fn(Blueprint $t) => $t->string('phone', 20)->nullable());
        $this->assertTrue($m->hasColumn('users', 'phone'));

        // Drop
        $m->drop('users');
        $this->assertFalse($m->hasTable('users'));
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/schema.php';
        $this->assertArrayHasKey('connection', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/schema.php';
        $this->assertArrayHasKey('error_table', $m);
    }
}
