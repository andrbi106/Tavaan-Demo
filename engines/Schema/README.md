# Schema

> تعریف ساختار دیتابیس با Blueprint (DDL Builder) برای MySQL/PostgreSQL/SQLite.

| فیلد | مقدار |
|------|------|
| Engine ID | `tavaan.engine.schema` |
| DOC-ID | `DOC-048` |
| Namespace | `Tavaan\Engine\Database\Schema` |
| اولویت | P0 (فاز M3) |
| Facade | `Schema` (accessor: `schema`) |
| مخزن | https://github.com/andrbi106/Tavaan-Engine-Schema |
| مستندات | https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/schema.md

## استفاده

```php
use Tavaan\Engine\Database\Schema\Blueprint;
use Tavaan\Engine\Database\Schema\SchemaBuilder;
use Tavaan\Engine\Database\Schema\SchemaManager;
use Tavaan\Engine\Database\Connection;

// مستقیم
$conn = new Connection('sqlite::memory:');
$schema = new SchemaBuilder($conn);

// Create
$schema->create('users', function (Blueprint $t): void {
    $t->id();
    $t->string('name', 190);
    $t->string('email')->unique();
    $t->string('password');
    $t->text('bio')->nullable();
    $t->decimal('balance', 18, 4)->default(0);
    $t->enum('status', ['active', 'banned'])->default('active');
    $t->json('settings')->nullable();
    $t->boolean('is_verified')->default(false);
    $t->timestamps();
    $t->softDeletes();
    $t->index(['status', 'created_at']);
});

// Alter
$schema->table('users', fn (Blueprint $t) => $t->string('phone', 20)->after('email'));

// Drop
$schema->drop('users');
$schema->dropIfExists('users');

// Introspection
$schema->hasTable('users');
$schema->hasColumn('users', 'email');
$schema->getTables();
$schema->getColumns('users');
$schema->getColumnType('users', 'name');

// Rename
$schema->rename('old_name', 'new_name');

// با SchemaManager (با ConnectionManager)
$cm = new ConnectionManager([...]);
$m = new SchemaManager([], $cm);
$m->create('posts', fn($t) => $t->id()->string('title'));
```

## انواع ستون

`id, ulid, uuid, integer, bigInteger, unsignedBigInteger, smallInteger, tinyInteger,
string, text, longText, boolean, decimal, double, float, date, dateTime, timestamp,
json, jsonb, enum, blob, binary, ipAddress, macAddress`

## تست‌ها

```bash
php bin/tvn test engines/schema
```

## مستندات

[docs/03-engines/schema.md](https://github.com/Derogaramin/Tavaan-Doc/blob/main/docs/03-engines/schema.md)

---
*Generated on 2026-11-03.*
