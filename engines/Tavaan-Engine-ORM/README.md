# ORM

> Active Record + Repository pattern با Casts، Soft Delete، Collection و dirty tracking.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.orm` |
| DOC-ID | `DOC-050` |
| Namespace | `Tavaan\Engine\Database\ORM` |
| اولویت | P0 (فاز M3) |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-ORM |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/orm.md

## استفاده

```php
use Tavaan\Engine\Database\ORM\Model;
use Tavaan\Engine\Database\ORM\Collection;
use Tavaan\Engine\Database\ORM\Repository;

// تعریف Model
final class User extends Model {
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'age'];
    protected array $hidden = ['password'];
    protected array $casts = ['age' => 'integer', 'settings' => 'json', 'is_active' => 'boolean'];
    protected bool $timestamps = true;
    protected bool $softDeletes = true;
}

// Active Record
$user = new User(['name' => 'Ali', 'age' => 25]);
$user->save();             // INSERT
$user->age = 26;
$user->save();             // UPDATE (only dirty fields)
$user->delete();           // Soft delete
$user->forceDelete();      // Hard delete

// Static methods
$user = User::find(1);
$users = User::all();
$users = User::where('age', '>', 18);
$count = User::count();
$user = User::create(['name' => 'Sara', 'age' => 30]);

// Dirty tracking
$user->name = 'New Name';
$user->isDirty();           // true
$user->isDirty('name');     // true
$user->isClean('age');      // true
$user->getDirty();           // ['name' => 'New Name']

// toArray با hidden
$user->toArray();           // ['name' => 'New Name', 'age' => 26, ...] (no password)

// Collection
$coll = new Collection($users);
$coll->count();
$coll->first();
$coll->pluck('name');       // ['Ali', 'Sara', ...]
$coll->keyBy('id');         // [1 => User, 2 => User]
$coll->where('age', 25);
$coll->sortBy('name');
$coll->map(fn($u) => $u->name);
$coll->filter(fn($u) => $u->age > 18);

// Repository pattern
final class UserRepository extends Repository {
    public function __construct() { parent::__construct(User::class); }
}
$repo = new UserRepository();
$user = $repo->find(1);
$users = $repo->all();
$user = $repo->create(['name' => 'Ali', 'age' => 25]);
$repo->save($user);
$repo->delete($user);
$count = $repo->count();
```

## مستندات

[docs/03-engines/orm.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/orm.md)

---
*Generated on 2026-11-03.*
