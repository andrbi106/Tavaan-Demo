<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Database\Connection;
use Tavaan\Engine\Database\ORM\Model;
use Tavaan\Engine\Database\ORM\ORMServiceProvider;

final class ORMFeatureTest extends TestCase
{
    public function test_end_to_end_crud_with_model(): void
    {
        $conn = new Connection('sqlite::memory:');
        $conn->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, body TEXT)');
        Model::setConnectionResolver(fn() => $conn);

        $provider = new ORMServiceProvider([]);
        $provider->register();

        $class = new class extends Model {
            protected string $table = 'posts';
            protected array $guarded = [];
            protected bool $timestamps = false;
        };

        // Create
        $post = new ($class::class);
        $post->fill(['title' => 'Hello', 'body' => 'World']);
        $this->assertTrue($post->save());
        $this->assertTrue($post->exists());
        $id = $post->getKey();
        $this->assertNotNull($id);

        // Read
        $found = ($class::class)::find($id);
        $this->assertNotNull($found);
        $this->assertSame('Hello', $found->title);

        // Update
        $found->title = 'Updated';
        $found->save();
        $again = ($class::class)::find($id);
        $this->assertSame('Updated', $again->title);

        // Delete
        $this->assertTrue($found->delete());
        $this->assertNull(($class::class)::find($id));

        // Count
        ($class::class)::create(['title' => 'New', 'body' => 'x']);
        $this->assertSame(1, ($class::class)::count());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/orm.php';
        $this->assertArrayHasKey('connection', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/orm.php';
        $this->assertArrayHasKey('error_model', $m);
    }
}
