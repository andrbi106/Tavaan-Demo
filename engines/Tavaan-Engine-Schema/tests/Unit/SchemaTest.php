<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Schema\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\ConnectionManager;
use Tavaan\Engine\Database\Schema\Blueprint;
use Tavaan\Engine\Database\Schema\ColumnDefinition;
use Tavaan\Engine\Database\Schema\SchemaBuilder;
use Tavaan\Engine\Database\Schema\SchemaManager;
use Tavaan\Engine\Database\Schema\SchemaServiceProvider;

/**
 * تست‌های Unit برای موتور Schema.
 *
 * @see docs/03-engines/schema.md (DOC-048)
 */
final class SchemaTest extends TestCase
{
    private function createBuilder(): SchemaBuilder
    {
        $conn = new Connection('sqlite::memory:');
        return new SchemaBuilder($conn);
    }

    public function test_column_definition_fluent(): void
    {
        $col = new ColumnDefinition('name', 'string', 100);
        $this->assertSame('name', $col->name);
        $this->assertSame('string', $col->type);
        $this->assertSame(100, $col->length);
        $this->assertFalse($col->nullable);

        $col2 = $col->nullable();
        $this->assertTrue($col2->nullable);
        $this->assertFalse($col->nullable);  // original unchanged
    }

    public function test_column_definition_default(): void
    {
        $col = (new ColumnDefinition('status', 'string'))->default('active');
        $this->assertSame('active', $col->default);
    }

    public function test_column_definition_unique(): void
    {
        $col = (new ColumnDefinition('email', 'string'))->unique();
        $this->assertTrue($col->unique);
    }

    public function test_column_definition_primary(): void
    {
        $col = (new ColumnDefinition('id', 'bigInteger'))->primary();
        $this->assertTrue($col->primary);
    }

    public function test_column_definition_auto_increment(): void
    {
        $col = (new ColumnDefinition('id', 'bigInteger'))->autoIncrement();
        $this->assertTrue($col->autoIncrement);
    }

    public function test_column_definition_unsigned(): void
    {
        $col = (new ColumnDefinition('count', 'bigInteger'))->unsigned();
        $this->assertTrue($col->unsigned);
    }

    public function test_column_definition_enum(): void
    {
        $col = (new ColumnDefinition('status', 'enum'))->enum(['active', 'banned']);
        $this->assertSame(['active', 'banned'], $col->enumValues);
    }

    public function test_blueprint_create_table(): void
    {
        $bp = new Blueprint('users');
        $bp->id();
        $bp->string('name', 100);
        $bp->string('email')->unique();
        $bp->timestamps();
        $this->assertCount(5, $bp->getColumns());
        $this->assertSame('users', $bp->getTable());
        $this->assertTrue($bp->isCreate());
    }

    public function test_blueprint_integer_types(): void
    {
        $bp = new Blueprint('t');
        $bp->integer('a');
        $bp->bigInteger('b');
        $bp->smallInteger('c');
        $bp->tinyInteger('d');
        $this->assertCount(4, $bp->getColumns());
        $this->assertSame('integer', $bp->getColumns()[0]->type);
        $this->assertSame('bigInteger', $bp->getColumns()[1]->type);
    }

    public function test_blueprint_text_types(): void
    {
        $bp = new Blueprint('t');
        $bp->text('body');
        $bp->longText('full');
        $this->assertSame('text', $bp->getColumns()[0]->type);
        $this->assertSame('longText', $bp->getColumns()[1]->type);
    }

    public function test_blueprint_numeric(): void
    {
        $bp = new Blueprint('t');
        $bp->decimal('price', 18, 4);
        $bp->double('rate');
        $bp->float('approx');
        $this->assertSame('decimal', $bp->getColumns()[0]->type);
        $this->assertSame(18, $bp->getColumns()[0]->precision);
        $this->assertSame(4, $bp->getColumns()[0]->scale);
    }

    public function test_blueprint_boolean(): void
    {
        $bp = new Blueprint('t');
        $bp->boolean('is_active');
        $this->assertSame('boolean', $bp->getColumns()[0]->type);
    }

    public function test_blueprint_enum(): void
    {
        $bp = new Blueprint('t');
        $bp->enum('status', ['active', 'banned']);
        $this->assertSame('enum', $bp->getColumns()[0]->type);
        $this->assertSame(['active', 'banned'], $bp->getColumns()[0]->enumValues);
    }

    public function test_blueprint_json(): void
    {
        $bp = new Blueprint('t');
        $bp->json('settings');
        $bp->jsonb('extra');
        $this->assertSame('json', $bp->getColumns()[0]->type);
        $this->assertSame('jsonb', $bp->getColumns()[1]->type);
    }

    public function test_blueprint_dates(): void
    {
        $bp = new Blueprint('t');
        $bp->date('birthday');
        $bp->dateTime('published_at');
        $bp->timestamp('updated_at');
        $this->assertSame('date', $bp->getColumns()[0]->type);
        $this->assertSame('dateTime', $bp->getColumns()[1]->type);
        $this->assertSame('timestamp', $bp->getColumns()[2]->type);
    }

    public function test_blueprint_special_types(): void
    {
        $bp = new Blueprint('t');
        $bp->uuid('id');
        $bp->ulid();
        $bp->ipAddress('ip');
        $bp->macAddress('mac');
        $bp->blob('data');
        $this->assertCount(5, $bp->getColumns());
    }

    public function test_blueprint_timestamps(): void
    {
        $bp = new Blueprint('t');
        $bp->timestamps();
        $cols = $bp->getColumns();
        $this->assertCount(2, $cols);
        $this->assertSame('created_at', $cols[0]->name);
        $this->assertSame('updated_at', $cols[1]->name);
        $this->assertTrue($cols[0]->nullable);
        $this->assertTrue($cols[1]->nullable);
    }

    public function test_blueprint_soft_deletes(): void
    {
        $bp = new Blueprint('t');
        $bp->softDeletes();
        $cols = $bp->getColumns();
        $this->assertSame('deleted_at', $cols[0]->name);
        $this->assertTrue($cols[0]->nullable);
    }

    public function test_blueprint_remember_token(): void
    {
        $bp = new Blueprint('t');
        $bp->rememberToken();
        $this->assertSame('remember_token', $bp->getColumns()[0]->name);
        $this->assertSame(100, $bp->getColumns()[0]->length);
    }

    public function test_blueprint_index(): void
    {
        $bp = new Blueprint('users');
        $bp->index(['status', 'created_at']);
        $this->assertCount(1, $bp->getIndexes());
        $this->assertStringContainsString('INDEX', $bp->getIndexes()[0]);
    }

    public function test_blueprint_unique_index(): void
    {
        $bp = new Blueprint('users');
        $bp->unique('email');
        $this->assertCount(1, $bp->getIndexes());
        $this->assertStringContainsString('UNIQUE', $bp->getIndexes()[0]);
    }

    public function test_blueprint_drop_column_command(): void
    {
        $bp = new Blueprint('users');
        $bp->dropColumn('old_column');
        $this->assertContains('DROP COLUMN old_column', $bp->getCommands());
    }

    public function test_blueprint_rename_column_command(): void
    {
        $bp = new Blueprint('users');
        $bp->renameColumn('from', 'to');
        $this->assertContains('RENAME COLUMN from TO to', $bp->getCommands());
    }

    // === SchemaBuilder (real SQLite) ===

    public function test_builder_create_table(): void
    {
        $b = $this->createBuilder();
        $b->create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
        });
        $this->assertTrue($b->hasTable('users'));
    }

    public function test_builder_drop(): void
    {
        $b = $this->createBuilder();
        $b->create('users', fn($t) => $t->id());
        $b->drop('users');
        $this->assertFalse($b->hasTable('users'));
    }

    public function test_builder_drop_if_exists(): void
    {
        $b = $this->createBuilder();
        $b->dropIfExists('nonexistent');  // no exception
        $this->assertTrue(true);
    }

    public function test_builder_rename(): void
    {
        $b = $this->createBuilder();
        $b->create('old', fn($t) => $t->id());
        $b->rename('old', 'new');
        $this->assertTrue($b->hasTable('new'));
        $this->assertFalse($b->hasTable('old'));
    }

    public function test_builder_has_column(): void
    {
        $b = $this->createBuilder();
        $b->create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
        });
        $this->assertTrue($b->hasColumn('users', 'name'));
        $this->assertFalse($b->hasColumn('users', 'missing'));
    }

    public function test_builder_get_tables(): void
    {
        $b = $this->createBuilder();
        $b->create('a', fn($t) => $t->id());
        $b->create('b', fn($t) => $t->id());
        $tables = $b->getTables();
        $this->assertContains('a', $tables);
        $this->assertContains('b', $tables);
    }

    public function test_builder_get_columns(): void
    {
        $b = $this->createBuilder();
        $b->create('users', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
        });
        $cols = $b->getColumns('users');
        $names = \array_column($cols, 'name');
        $this->assertContains('id', $names);
        $this->assertContains('name', $names);
    }

    public function test_builder_get_column_type(): void
    {
        $b = $this->createBuilder();
        $b->create('users', function (Blueprint $t): void {
            $t->string('name');
        });
        $type = $b->getColumnType('users', 'name');
        $this->assertNotNull($type);
    }

    public function test_builder_alter_table(): void
    {
        $b = $this->createBuilder();
        $b->create('users', fn($t) => $t->id());
        $b->table('users', fn(Blueprint $t) => $t->string('phone'));
        $this->assertTrue($b->hasColumn('users', 'phone'));
    }

    public function test_builder_full_workflow(): void
    {
        $b = $this->createBuilder();
        $b->create('products', function (Blueprint $t): void {
            $t->id();
            $t->string('name', 190);
            $t->decimal('price', 18, 4);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->index(['is_active', 'created_at']);
        });
        $this->assertTrue($b->hasTable('products'));
        $this->assertTrue($b->hasColumn('products', 'name'));
        $this->assertTrue($b->hasColumn('products', 'price'));
        $this->assertTrue($b->hasColumn('products', 'is_active'));
    }

    // === SchemaManager ===

    public function test_manager_with_connection(): void
    {
        $cm = new ConnectionManager([
            'default' => 'default',
            'connections' => ['default' => ['driver' => 'sqlite', 'database' => ':memory:']],
        ]);
        $m = new SchemaManager([], $cm);
        $m->create('test', fn($t) => $t->id());
        $this->assertTrue($m->hasTable('test'));
    }

    public function test_service_provider(): void
    {
        $provider = new SchemaServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(SchemaManager::class, $provider->getManager());
        $this->assertContains(SchemaManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/schema.php';
        $this->assertArrayHasKey('connection', $config);
    }
}
