<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\ConnectionManager;
use Tavaan\Engine\Database\ORM\Model;
use Tavaan\Engine\Database\ORM\Collection;
use Tavaan\Engine\Database\ORM\Repository;
use Tavaan\Engine\Database\ORM\ORMServiceProvider;

/**
 * تست‌های Unit برای موتور ORM.
 *
 * @see docs/03-engines/orm.md (DOC-050)
 */
final class ORMTest extends TestCase
{
    private function createConnection(): Connection
    {
        $conn = new Connection('sqlite::memory:');
        $conn->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, age INTEGER, email TEXT, settings TEXT, deleted_at TEXT, created_at TEXT, updated_at TEXT)');
        return $conn;
    }

    private function setupResolver(Connection $conn): void
    {
        Model::setConnectionResolver(fn() => $conn);
    }

    public function test_model_fill(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $fillable = ['name', 'age'];
        };
        $m->fill(['name' => 'Ali', 'age' => 25, 'id' => 1]);
        $this->assertSame('Ali', $m->name);
        $this->assertSame(25, $m->age);
        // id is guarded
        $this->assertNull($m->getAttribute('id'));
    }

    public function test_model_fill_with_empty_fillable_allows_all(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
        };
        $m->fill(['name' => 'x', 'age' => 1]);
        $this->assertSame('x', $m->getAttribute('name'));
    }

    public function test_model_guarded_blocks(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = ['id', 'password'];
            protected array $fillable = [];
        };
        $m->fill(['name' => 'Ali', 'id' => 1, 'password' => 'secret']);
        $this->assertSame('Ali', $m->getAttribute('name'));
        $this->assertNull($m->getAttribute('id'));
        $this->assertNull($m->getAttribute('password'));
    }

    public function test_model_set_get_attribute(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
        };
        $m->name = 'Ali';
        $this->assertSame('Ali', $m->name);
        $this->assertTrue(isset($m->name));
        $this->assertFalse(isset($m->missing));
    }

    public function test_model_is_fillable(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $fillable = ['name', 'age'];
        };
        $this->assertTrue($m->isFillable('name'));
        $this->assertTrue($m->isFillable('age'));
        $this->assertFalse($m->isFillable('id'));
        $this->assertFalse($m->isFillable('password'));
    }

    public function test_model_get_attributes(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
        };
        $m->fill(['name' => 'Ali', 'age' => 25]);
        $this->assertSame(['name' => 'Ali', 'age' => 25], $m->getAttributes());
    }

    public function test_model_to_array_with_hidden(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected array $hidden = ['password'];
        };
        $m->fill(['name' => 'Ali', 'password' => 'secret']);
        $arr = $m->toArray();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayNotHasKey('password', $arr);
    }

    public function test_model_dirty_detection(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
        };
        $m->fill(['name' => 'Ali']);
        $m->syncOriginal();
        $this->assertFalse($m->isDirty());
        $m->name = 'Sara';
        $this->assertTrue($m->isDirty());
        $this->assertTrue($m->isDirty('name'));
        $this->assertFalse($m->isDirty('age'));
        $this->assertSame(['name' => 'Sara'], $m->getDirty());
    }

    public function test_model_is_clean(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
        };
        $m->fill(['name' => 'Ali']);
        $m->syncOriginal();
        $this->assertTrue($m->isClean());
        $this->assertTrue($m->isClean('name'));
    }

    public function test_model_sync_original(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
        };
        $m->fill(['name' => 'Ali']);
        $m->syncOriginal();
        $this->assertSame(['name' => 'Ali'], $m->getOriginal());
    }

    public function test_model_casts_integer(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected array $casts = ['age' => 'integer'];
        };
        $m->fill(['age' => '25']);
        $this->assertSame(25, $m->age);
        $this->assertIsInt($m->age);
    }

    public function test_model_casts_boolean(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected array $casts = ['is_active' => 'boolean'];
        };
        $m->fill(['is_active' => 1]);
        $this->assertTrue($m->getAttribute('is_active'));
    }

    public function test_model_casts_json(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected array $casts = ['settings' => 'json'];
        };
        $m->fill(['settings' => '{"theme":"dark"}']);
        $this->assertSame(['theme' => 'dark'], $m->getAttribute('settings'));
    }

    public function test_model_casts_array(): void
    {
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected array $casts = ['tags' => 'array'];
        };
        $m->fill(['tags' => '["a","b"]']);
        $this->assertSame(['a', 'b'], $m->getAttribute('tags'));
    }

    // === Database-backed tests ===

    public function test_model_save_new(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $m = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $m->fill(['name' => 'Ali', 'age' => 25]);
        $this->assertFalse($m->exists());
        $this->assertTrue($m->save());
        $this->assertTrue($m->exists());
        $this->assertNotNull($m->getKey());
    }

    public function test_model_find(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $m = new ($class::class);
        $m->fill(['name' => 'Ali', 'age' => 25]);
        $m->save();
        $found = ($class::class)::find($m->getKey());
        $this->assertNotNull($found);
        $this->assertSame('Ali', $found->name);
    }

    public function test_model_find_returns_null(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $this->assertNull(($class::class)::find(999));
    }

    public function test_model_all(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        ($class::class)::create(['name' => 'Sara', 'age' => 30]);
        $all = ($class::class)::all();
        $this->assertCount(2, $all);
    }

    public function test_model_where(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        ($class::class)::create(['name' => 'Sara', 'age' => 30]);
        $results = ($class::class)::where('age', '>', 26);
        $this->assertCount(1, $results);
        $this->assertSame('Sara', $results[0]->name);
    }

    public function test_model_count(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        ($class::class)::create(['name' => 'Sara', 'age' => 30]);
        $this->assertSame(2, ($class::class)::count());
    }

    public function test_model_update(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $m = new ($class::class);
        $m->fill(['name' => 'Ali', 'age' => 25]);
        $m->save();
        $m->age = 26;
        $this->assertTrue($m->isDirty('age'));
        $m->save();
        $found = ($class::class)::find($m->getKey());
        $this->assertSame(26, $found->age);
    }

    public function test_model_delete(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
            protected bool $softDeletes = false;
        };
        $m = ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        $this->assertTrue($m->delete());
        $this->assertFalse($m->exists());
        $this->assertNull(($class::class)::find($m->getKey()));
    }

    public function test_model_soft_delete(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
            protected bool $softDeletes = true;
        };
        $m = ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        $this->assertTrue($m->delete());
        // find با soft delete نباید آن را برگرداند
        $this->assertNull(($class::class)::find($m->getKey()));
        // اما در جدول باید موجود باشد
        $row = $conn->selectOne("SELECT * FROM users WHERE id = ?", [$m->getKey()]);
        $this->assertNotNull($row);
        $this->assertNotNull($row['deleted_at']);
    }

    public function test_model_create_static(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $m = ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        $this->assertNotNull($m);
        $this->assertTrue($m->exists());
        $this->assertSame('Ali', $m->name);
    }

    // === Collection ===

    public function test_collection_basic(): void
    {
        $c = new Collection();
        $this->assertTrue($c->isEmpty());
        $this->assertSame(0, $c->count());
    }

    public function test_collection_first_last(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m2 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m1->name = 'a';
        $m2->name = 'b';
        $c = new Collection([$m1, $m2]);
        $this->assertSame($m1, $c->first());
        $this->assertSame($m2, $c->last());
    }

    public function test_collection_count(): void
    {
        $c = new Collection([1, 2, 3]);
        $this->assertSame(3, $c->count());
    }

    public function test_collection_pluck(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m2 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m1->id = 1;
        $m2->id = 2;
        $c = new Collection([$m1, $m2]);
        $this->assertSame([1, 2], $c->pluck('id'));
    }

    public function test_collection_key_by(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m2 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m1->id = 1;
        $m2->id = 2;
        $c = new Collection([$m1, $m2]);
        $result = $c->keyBy('id');
        $this->assertSame($m1, $result[1]);
        $this->assertSame($m2, $result[2]);
    }

    public function test_collection_each(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m2 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $visited = [];
        $c = new Collection([$m1, $m2]);
        $c->each(function ($m) use (&$visited) { $visited[] = $m; });
        $this->assertCount(2, $visited);
    }

    public function test_collection_map(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m1->id = 1;
        $c = new Collection([$m1]);
        $result = $c->map(fn($m) => $m->id * 2);
        $this->assertSame([2], $result);
    }

    public function test_collection_filter(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m2 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m1->id = 1;
        $m2->id = 2;
        $c = new Collection([$m1, $m2]);
        $filtered = $c->filter(fn($m) => $m->id > 1);
        $this->assertSame(1, $filtered->count());
    }

    public function test_collection_sort_by(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m2 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m1->order = 2;
        $m2->order = 1;
        $c = new Collection([$m1, $m2]);
        $sorted = $c->sortBy('order');
        $this->assertSame(1, $sorted->first()->getAttribute('order'));
    }

    public function test_collection_to_array(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $m1->id = 1;
        $m1->name = 'Ali';
        $c = new Collection([$m1]);
        $arr = $c->toArray();
        $this->assertSame([['id' => 1, 'name' => 'Ali']], $arr);
    }

    public function test_collection_is_not_empty(): void
    {
        $m1 = new class extends Model { protected string $table = 't'; protected array $guarded = []; };
        $c = new Collection([$m1]);
        $this->assertTrue($c->isNotEmpty());
    }

    // === Repository ===

    public function test_repository_find(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $m = ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        $repo = new class($class::class) extends Repository {};
        $found = $repo->find($m->getKey());
        $this->assertNotNull($found);
        $this->assertSame('Ali', $found->name);
    }

    public function test_repository_all(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        ($class::class)::create(['name' => 'Sara', 'age' => 30]);
        $repo = new class($class::class) extends Repository {};
        $this->assertCount(2, $repo->all());
    }

    public function test_repository_create(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $repo = new class($class::class) extends Repository {};
        $m = $repo->create(['name' => 'Ali', 'age' => 25]);
        $this->assertNotNull($m);
        $this->assertTrue($m->exists());
    }

    public function test_repository_count(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        ($class::class)::create(['name' => 'Ali', 'age' => 25]);
        $repo = new class($class::class) extends Repository {};
        $this->assertSame(1, $repo->count());
    }

    public function test_repository_save(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };
        $repo = new class($class::class) extends Repository {};
        $m = new ($class::class);
        $m->fill(['name' => 'Ali', 'age' => 25]);
        $this->assertTrue($repo->save($m));
    }

    public function test_repository_delete(): void
    {
        $conn = $this->createConnection();
        $this->setupResolver($conn);
        $class = new class extends Model {
            protected string $table = 'users';
            protected array $guarded = [];
            protected bool $timestamps = false;
            protected bool $softDeletes = false;
        };
        $repo = new class($class::class) extends Repository {};
        $m = $repo->create(['name' => 'Ali', 'age' => 25]);
        $this->assertTrue($repo->delete($m));
        $this->assertNull($repo->find($m->getKey()));
    }

    // === ServiceProvider ===

    public function test_service_provider_provides(): void
    {
        $provider = new ORMServiceProvider([]);
        $this->assertContains(Model::class, $provider->provides());
        $this->assertContains(Collection::class, $provider->provides());
        $this->assertContains(Repository::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/orm.php';
        $this->assertArrayHasKey('connection', $config);
        $this->assertArrayHasKey('strict_mode', $config);
    }
}
