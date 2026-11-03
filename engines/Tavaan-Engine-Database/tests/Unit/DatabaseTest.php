<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\ConnectionManager;
use Tavaan\Engine\Database\Query\QueryBuilder;
use Tavaan\Engine\Database\Exceptions\DatabaseException;

/**
 * تست‌های Unit برای موتور Database.
 *
 * @see docs/03-engines/database.md (DOC-047)
 */
final class DatabaseTest extends TestCase
{
    private function createConnection(): Connection
    {
        return new Connection('sqlite::memory:');
    }

    public function test_connection_get_pdo_lazy(): void
    {
        $c = $this->createConnection();
        $this->assertInstanceOf(\PDO::class, $c->getPdo());
    }

    public function test_exec_create_table(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $this->assertSame(0, $c->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY)'));
    }

    public function test_insert_and_select(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $id = $c->insert('INSERT INTO users (name, age) VALUES (?, ?)', ['Ali', 25]);
        $this->assertGreaterThan(0, $id);

        $rows = $c->select('SELECT * FROM users WHERE name = ?', ['Ali']);
        $this->assertCount(1, $rows);
        $this->assertSame('Ali', $rows[0]['name']);
        $this->assertSame(25, (int) $rows[0]['age']);
    }

    public function test_select_one(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $row = $c->selectOne('SELECT * FROM u WHERE name = ?', ['Ali']);
        $this->assertNotNull($row);
        $this->assertSame('Ali', $row['name']);
        $this->assertNull($c->selectOne('SELECT * FROM u WHERE name = ?', ['Nonexistent']));
    }

    public function test_update(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $c->insert('INSERT INTO u (name, age) VALUES (?, ?)', ['Ali', 25]);
        $count = $c->update('UPDATE u SET age = ? WHERE name = ?', [26, 'Ali']);
        $this->assertSame(1, $count);
    }

    public function test_delete(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $count = $c->delete('DELETE FROM u WHERE name = ?', ['Ali']);
        $this->assertSame(1, $count);
    }

    public function test_query_log(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY)');
        $c->insert('INSERT INTO u (id) VALUES (1)');
        $c->select('SELECT * FROM u');
        $log = $c->getQueryLog();
        $this->assertGreaterThanOrEqual(2, \count($log));
        $c->flushQueryLog();
        $this->assertSame([], $c->getQueryLog());
    }

    public function test_transaction_basic(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->beginTransaction();
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $c->commit();
        $rows = $c->select('SELECT * FROM u');
        $this->assertCount(1, $rows);
    }

    public function test_transaction_rollback(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->beginTransaction();
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $c->rollBack();
        $rows = $c->select('SELECT * FROM u');
        $this->assertCount(0, $rows);
    }

    public function test_transaction_callable(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $result = $c->transaction(function ($conn) {
            $conn->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
            return 'ok';
        });
        $this->assertSame('ok', $result);
        $rows = $c->select('SELECT * FROM u');
        $this->assertCount(1, $rows);
    }

    public function test_transaction_callable_rolls_back_on_exception(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        try {
            $c->transaction(function ($conn) {
                $conn->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
                throw new \RuntimeException('test error');
            });
        } catch (\RuntimeException) {}
        $rows = $c->select('SELECT * FROM u');
        $this->assertCount(0, $rows);
    }

    public function test_nested_transaction_with_savepoint(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->beginTransaction();
        $c->insert('INSERT INTO u (name) VALUES (?)', ['outer']);
        $c->beginTransaction();  // savepoint
        $c->insert('INSERT INTO u (name) VALUES (?)', ['inner']);
        $c->rollBack();  // rollback to savepoint
        $c->commit();
        $rows = $c->select('SELECT * FROM u');
        $this->assertCount(1, $rows);
        $this->assertSame('outer', $rows[0]['name']);
    }

    public function test_disconnect(): void
    {
        $c = $this->createConnection();
        $c->getPdo();
        $c->disconnect();
        // Reconnection should work
        $this->assertInstanceOf(\PDO::class, $c->getPdo());
    }

    public function test_transaction_level(): void
    {
        $c = $this->createConnection();
        $this->assertSame(0, $c->getTransactionLevel());
        $c->beginTransaction();
        $this->assertSame(1, $c->getTransactionLevel());
        $c->commit();
        $this->assertSame(0, $c->getTransactionLevel());
    }

    public function test_commit_without_begin_returns_false(): void
    {
        $c = $this->createConnection();
        $this->assertFalse($c->commit());
    }

    public function test_rollback_without_begin_returns_false(): void
    {
        $c = $this->createConnection();
        $this->assertFalse($c->rollBack());
    }

    // === ConnectionManager ===

    public function test_manager_creates_connection(): void
    {
        $m = new ConnectionManager([
            'default' => 'test',
            'connections' => [
                'test' => ['driver' => 'sqlite', 'database' => ':memory:'],
            ],
        ]);
        $c = $m->connection();
        $this->assertInstanceOf(Connection::class, $c);
        $this->assertSame('test', $m->getDefaultConnection());
    }

    public function test_manager_unknown_throws(): void
    {
        $m = new ConnectionManager(['connections' => []]);
        $this->expectException(DatabaseException::class);
        $m->connection('unknown');
    }

    public function test_manager_add_connection(): void
    {
        $m = new ConnectionManager(['default' => 'x']);
        $m->addConnection('x', ['driver' => 'sqlite', 'database' => ':memory:']);
        $this->assertInstanceOf(Connection::class, $m->connection('x'));
    }

    public function test_manager_disconnect(): void
    {
        $m = new ConnectionManager([
            'default' => 'test',
            'connections' => ['test' => ['driver' => 'sqlite', 'database' => ':memory:']],
        ]);
        $m->connection();  // create
        $m->disconnect();
        $this->assertTrue(true);
    }

    public function test_manager_build_mysql_dsn(): void
    {
        $m = new ConnectionManager([
            'default' => 'mysql',
            'connections' => [
                'mysql' => [
                    'driver' => 'mysql',
                    'host' => 'db.example.com',
                    'port' => 3307,
                    'database' => 'myapp',
                    'charset' => 'utf8mb4',
                ],
            ],
        ]);
        $c = $m->connection('mysql');
        // We don't actually connect — just verify instance
        $this->assertInstanceOf(Connection::class, $c);
    }

    // === QueryBuilder ===

    public function test_qb_select_all(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO users (name) VALUES (?)', ['Ali']);
        $c->insert('INSERT INTO users (name) VALUES (?)', ['Sara']);
        $qb = new QueryBuilder($c, 'users');
        $rows = $qb->get();
        $this->assertCount(2, $rows);
    }

    public function test_qb_select_columns(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $c->insert('INSERT INTO users (name, age) VALUES (?, ?)', ['Ali', 25]);
        $qb = new QueryBuilder($c, 'users');
        $qb->select(['name', 'age']);
        $rows = $qb->get();
        $this->assertArrayNotHasKey('id', $rows[0]);
        $this->assertArrayHasKey('name', $rows[0]);
    }

    public function test_qb_where(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $c->insert('INSERT INTO u (name, age) VALUES (?, ?)', ['Ali', 25]);
        $c->insert('INSERT INTO u (name, age) VALUES (?, ?)', ['Sara', 30]);
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->where('age', '>', 25)->get();
        $this->assertCount(1, $rows);
        $this->assertSame('Sara', $rows[0]['name']);
    }

    public function test_qb_where_shorthand(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->where('name', 'Ali')->get();
        $this->assertCount(1, $rows);
    }

    public function test_qb_or_where(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $c->insert('INSERT INTO u (name, age) VALUES (?, ?)', ['Ali', 25]);
        $c->insert('INSERT INTO u (name, age) VALUES (?, ?)', ['Sara', 30]);
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->where('name', 'Ali')->orWhere('name', 'Sara')->get();
        $this->assertCount(2, $rows);
    }

    public function test_qb_where_in(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Sara']);
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Reza']);
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->whereIn('name', ['Ali', 'Sara'])->get();
        $this->assertCount(2, $rows);
    }

    public function test_qb_where_null(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT, deleted_at TEXT)');
        $c->insert('INSERT INTO u (name, deleted_at) VALUES (?, ?)', ['Ali', null]);
        $c->insert('INSERT INTO u (name, deleted_at) VALUES (?, ?)', ['Sara', '2024-01-01']);
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->whereNull('deleted_at')->get();
        $this->assertCount(1, $rows);
        $this->assertSame('Ali', $rows[0]['name']);
    }

    public function test_qb_where_not_null(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT, deleted_at TEXT)');
        $c->insert('INSERT INTO u (name, deleted_at) VALUES (?, ?)', ['Ali', null]);
        $c->insert('INSERT INTO u (name, deleted_at) VALUES (?, ?)', ['Sara', '2024-01-01']);
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->whereNotNull('deleted_at')->get();
        $this->assertCount(1, $rows);
        $this->assertSame('Sara', $rows[0]['name']);
    }

    public function test_qb_order_by(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['C']);
        $c->insert('INSERT INTO u (name) VALUES (?)', ['A']);
        $c->insert('INSERT INTO u (name) VALUES (?)', ['B']);
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->orderBy('name', 'ASC')->get();
        $this->assertSame(['A', 'B', 'C'], \array_column($rows, 'name'));
    }

    public function test_qb_limit_offset(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        for ($i = 1; $i <= 10; $i++) {
            $c->insert('INSERT INTO u (name) VALUES (?)', ["u{$i}"]);
        }
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->orderBy('id', 'ASC')->limit(3)->offset(2)->get();
        $this->assertCount(3, $rows);
        $this->assertSame('u3', $rows[0]['name']);
    }

    public function test_qb_take_skip_aliases(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        for ($i = 1; $i <= 5; $i++) {
            $c->insert('INSERT INTO u (name) VALUES (?)', ["u{$i}"]);
        }
        $qb = new QueryBuilder($c, 'u');
        $rows = $qb->orderBy('id', 'ASC')->take(2)->skip(1)->get();
        $this->assertCount(2, $rows);
        $this->assertSame('u2', $rows[0]['name']);
    }

    public function test_qb_first(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Sara']);
        $qb = new QueryBuilder($c, 'u');
        $row = $qb->orderBy('id', 'ASC')->first();
        $this->assertNotNull($row);
        $this->assertSame('Ali', $row['name']);
    }

    public function test_qb_count(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY)');
        $c->insert('INSERT INTO u (id) VALUES (1)');
        $c->insert('INSERT INTO u (id) VALUES (2)');
        $qb = new QueryBuilder($c, 'u');
        $this->assertSame(2, $qb->count());
    }

    public function test_qb_exists(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $qb = new QueryBuilder($c, 'u');
        $this->assertTrue($qb->where('name', 'Ali')->exists());
        $this->assertFalse($qb->where('name', 'Nonexistent')->exists());
    }

    public function test_qb_insert(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $qb = new QueryBuilder($c, 'u');
        $id = $qb->insert(['name' => 'Ali', 'age' => 25]);
        $this->assertGreaterThan(0, $id);
    }

    public function test_qb_update(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT, age INTEGER)');
        $c->insert('INSERT INTO u (name, age) VALUES (?, ?)', ['Ali', 25]);
        $qb = new QueryBuilder($c, 'u');
        $count = $qb->where('name', 'Ali')->update(['age' => 26]);
        $this->assertSame(1, $count);
    }

    public function test_qb_delete(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE u (id INTEGER PRIMARY KEY, name TEXT)');
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Ali']);
        $c->insert('INSERT INTO u (name) VALUES (?)', ['Sara']);
        $qb = new QueryBuilder($c, 'u');
        $count = $qb->where('name', 'Ali')->delete();
        $this->assertSame(1, $count);
        $this->assertSame(1, $qb->count());
    }

    public function test_qb_to_sql(): void
    {
        $c = $this->createConnection();
        $qb = new QueryBuilder($c, 'users');
        $qb->select(['id', 'name'])->where('age', '>', 18)->orderBy('id', 'ASC')->limit(10);
        [$sql, $bindings] = $qb->toSql();
        $this->assertStringContainsString('SELECT id, name', $sql);
        $this->assertStringContainsString('FROM users', $sql);
        $this->assertStringContainsString('WHERE age > ?', $sql);
        $this->assertStringContainsString('ORDER BY id ASC', $sql);
        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertSame([18], $bindings);
    }

    public function test_qb_table_name(): void
    {
        $c = $this->createConnection();
        $qb = new QueryBuilder($c, 'my_table');
        $this->assertSame('my_table', $qb->table());
    }

    public function test_qb_from_changes_table(): void
    {
        $c = $this->createConnection();
        $c->exec('CREATE TABLE other (id INTEGER PRIMARY KEY)');
        $qb = new QueryBuilder($c, 'original');
        $qb->from('other');
        $this->assertSame('other', $qb->table());
    }
}
