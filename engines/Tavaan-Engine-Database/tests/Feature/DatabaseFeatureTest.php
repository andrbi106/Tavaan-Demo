<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\ConnectionManager;
use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\DatabaseServiceProvider;

final class DatabaseFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new DatabaseServiceProvider([
            'default' => 'test',
            'connections' => ['test' => ['driver' => 'sqlite', 'database' => ':memory:']],
        ]);
        $provider->register();
        $this->assertInstanceOf(ConnectionManager::class, $provider->getManager());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new DatabaseServiceProvider([]);
        $this->assertContains(ConnectionManager::class, $provider->provides());
        $this->assertContains(Connection::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->assertArrayHasKey('default', $config);
        $this->assertArrayHasKey('connections', $config);
        $this->assertArrayHasKey('query_log', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/database.php';
        $this->assertArrayHasKey('error_connection', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/database.php';
        $this->assertArrayHasKey('error_connection', $messages);
    }

    public function test_end_to_end_crud_workflow(): void
    {
        $provider = new DatabaseServiceProvider([
            'default' => 'test',
            'connections' => ['test' => ['driver' => 'sqlite', 'database' => ':memory:']],
        ]);
        $provider->register();
        $db = $provider->getManager()->connection();
        $db->exec('CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, price REAL)');
        $qb = new \Tavaan\Engine\Database\Query\QueryBuilder($db, 'products');
        $id = $qb->insert(['name' => 'Laptop', 'price' => 50000]);
        $this->assertGreaterThan(0, $id);
        $rows = (new \Tavaan\Engine\Database\Query\QueryBuilder($db, 'products'))->get();
        $this->assertCount(1, $rows);
        $this->assertSame('Laptop', $rows[0]['name']);
    }
}
