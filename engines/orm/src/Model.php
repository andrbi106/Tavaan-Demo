<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM;

/**
 * Model — کلاس پایه Active Record.
 *
 * @see docs/03-engines/orm.md (DOC-050)
 */
abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected bool $incrementing = true;
    protected bool $timestamps = true;
    protected bool $softDeletes = false;

    /** @var list<string> */
    protected array $fillable = [];
    /** @var list<string> */
    protected array $guarded = ['id'];
    /** @var list<string> */
    protected array $hidden = [];
    /** @var array<string, string> */
    protected array $casts = [];

    /** @var array<string, mixed> */
    private array $attributes = [];

    /** @var array<string, mixed> */
    private array $original = [];

    private bool $exists = false;

    private static mixed $connectionResolver = null;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $key = (string) $key;
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    public function isFillable(string|int $key): bool
    {
        if (\in_array($key, $this->guarded, true)) return false;
        if (empty($this->fillable)) return true;
        return \in_array($key, $this->fillable, true);
    }

    public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $this->cast($key, $value);
        return $this;
    }

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->attributes as $key => $value) {
            if (\in_array($key, $this->hidden, true)) continue;
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOriginal(): array
    {
        return $this->original;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDirty(): array
    {
        $dirty = [];
        foreach ($this->attributes as $key => $value) {
            if (!\array_key_exists($key, $this->original) || $this->original[$key] !== $value) {
                $dirty[$key] = $value;
            }
        }
        return $dirty;
    }

    public function isDirty(?string $key = null): bool
    {
        if ($key === null) return !empty($this->getDirty());
        return \array_key_exists($key, $this->getDirty());
    }

    public function isClean(?string $key = null): bool
    {
        return !$this->isDirty($key);
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function setExists(bool $exists): self
    {
        $this->exists = $exists;
        return $this;
    }

    public function syncOriginal(): self
    {
        $this->original = $this->attributes;
        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    public function getKey(): mixed
    {
        return $this->getAttribute($this->primaryKey);
    }

    // === Persistence ===

    public function save(): bool
    {
        $conn = self::resolveConnection();
        if ($conn === null) return false;

        if ($this->exists) {
            $dirty = $this->getDirty();
            if (empty($dirty)) return true;
            if ($this->timestamps) {
                $dirty['updated_at'] = \date('Y-m-d H:i:s');
            }
            $sql = "UPDATE {$this->table} SET ";
            $sets = [];
            $bindings = [];
            foreach ($dirty as $col => $val) {
                $sets[] = "{$col} = ?";
                $bindings[] = $val;
            }
            $bindings[] = $this->getKey();
            $sql .= \implode(', ', $sets) . " WHERE {$this->primaryKey} = ?";
            $conn->update($sql, $bindings);
        } else {
            if ($this->timestamps) {
                $this->attributes['created_at'] = $this->attributes['created_at'] ?? \date('Y-m-d H:i:s');
                $this->attributes['updated_at'] = $this->attributes['updated_at'] ?? \date('Y-m-d H:i:s');
            }
            $attrs = $this->attributes;
            $sql = "INSERT INTO {$this->table} (" . \implode(', ', \array_keys($attrs)) . ') VALUES (' . \implode(', ', \array_fill(0, \count($attrs), '?')) . ')';
            $bindings = \array_values($attrs);
            $id = $conn->insert($sql, $bindings);
            if ($this->incrementing) {
                $this->setAttribute($this->primaryKey, $id);
            }
            $this->exists = true;
        }
        $this->syncOriginal();
        return true;
    }

    public function delete(): bool
    {
        $conn = self::resolveConnection();
        if ($conn === null || !$this->exists) return false;
        if ($this->softDeletes) {
            $conn->update(
                "UPDATE {$this->table} SET deleted_at = ? WHERE {$this->primaryKey} = ?",
                [\date('Y-m-d H:i:s'), $this->getKey()]
            );
        } else {
            $conn->delete(
                "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?",
                [$this->getKey()]
            );
        }
        $this->exists = false;
        return true;
    }

    public function forceDelete(): bool
    {
        $conn = self::resolveConnection();
        if ($conn === null || !$this->exists) return false;
        $conn->delete("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?", [$this->getKey()]);
        $this->exists = false;
        return true;
    }

    // === Static Query Methods ===

    public static function find(int|string $id): ?static
    {
        $instance = new static();
        $conn = self::resolveConnection();
        if ($conn === null) return null;
        $sql = "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?";
        if ($instance->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $row = $conn->selectOne($sql, [$id]);
        if ($row === null) return null;
        $model = new static($row);
        $model->setExists(true)->syncOriginal();
        return $model;
    }

    public static function all(): array
    {
        $instance = new static();
        $conn = self::resolveConnection();
        if ($conn === null) return [];
        $sql = "SELECT * FROM {$instance->table}";
        if ($instance->softDeletes) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        $rows = $conn->select($sql);
        $models = [];
        foreach ($rows as $row) {
            $m = new static($row);
            $m->setExists(true)->syncOriginal();
            $models[] = $m;
        }
        return $models;
    }

    public static function where(string $column, mixed $operator = '=', mixed $value = null): array
    {
        $instance = new static();
        $conn = self::resolveConnection();
        if ($conn === null) return [];
        if ($value === null && !\in_array($operator, ['<', '>', '<=', '>=', '!='], true)) {
            $value = $operator;
            $operator = '=';
        }
        $sql = "SELECT * FROM {$instance->table} WHERE {$column} {$operator} ?";
        if ($instance->softDeletes) {
            $sql .= " AND deleted_at IS NULL";
        }
        $rows = $conn->select($sql, [$value]);
        $models = [];
        foreach ($rows as $row) {
            $m = new static($row);
            $m->setExists(true)->syncOriginal();
            $models[] = $m;
        }
        return $models;
    }

    public static function create(array $attributes): ?static
    {
        $model = new static($attributes);
        if ($model->save()) return $model;
        return null;
    }

    public static function count(): int
    {
        $instance = new static();
        $conn = self::resolveConnection();
        if ($conn === null) return 0;
        $sql = "SELECT COUNT(*) AS count FROM {$instance->table}";
        if ($instance->softDeletes) {
            $sql .= " WHERE deleted_at IS NULL";
        }
        $row = $conn->selectOne($sql);
        return (int) ($row['count'] ?? 0);
    }

    // === Connection Resolver ===

    public static function setConnectionResolver(callable $resolver): void
    {
        self::$connectionResolver = $resolver;
    }

    private static function resolveConnection(): ?\Tavaan\Engine\Database\Connection
    {
        if (self::$connectionResolver === null) return null;
        return (self::$connectionResolver)();
    }

    // === Casts ===

    private function cast(string $key, mixed $value): mixed
    {
        $type = $this->casts[$key] ?? null;
        if ($type === null) return $value;
        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'bool', 'boolean' => (bool) $value,
            'string' => (string) $value,
            'array' => \is_array($value) ? $value : (\is_string($value) ? \json_decode($value, true) ?? [] : []),
            'json' => \is_string($value) ? \json_decode($value, true) : $value,
            'datetime' => $value instanceof \DateTime ? $value : new \DateTime((string) $value),
            default => $value,
        };
    }

    /**
     * addGlobalScope
     */
    public function addGlobalScope(string $name, callable $scope): void
    {
        self::$globalScopes[static::class][$name] = $scope;
    }
    /**
     * chunk
     */
    public function chunk(int $count, callable $callback): bool
    {
        $page = 0;
        while (true) {
            $results = static::where("id", ">", $page * $count);
            if (empty($results)) break;
            foreach ($results as $model) {
                $callback($model);
            }
            if (count($results) < $count) break;
            $page++;
        }
        return true;
    }
    /**
     * findOrFail
     */
    public static function findOrFail(mixed $id): static
    {
        $model = static::find($id);
        if ($model === null) {
            throw new \RuntimeException("No query results for model [" . static::class . "] for id [{$id}].");
        }
        return $model;
    }
    /**
     * firstOrCreate
     */
    public static function firstOrCreate(array $values, array $attributes = []): static
    {
        $instance = new static();
        $conn = self::resolveConnection();
        if ($conn === null) return new static($values);
        $sql = "SELECT * FROM {$instance->table} WHERE " . implode(" = ? AND ", array_keys($values)) . " = ?";
        $row = $conn->selectOne($sql, array_values($values));
        if ($row !== null) {
            $m = new static($row);
            return $m->setExists(true)->syncOriginal();
        }
        return static::create(array_merge($values, $attributes));
    }
    /**
     * lazy
     */
    public static function lazy(int $chunkSize = 1000): \Generator
    {
        $page = 0;
        while (true) {
            $models = static::all();
            if (empty($models)) break;
            foreach ($models as $model) yield $model;
            break;
        }
    }
    /**
     * load
     */
    public function load(array|string $relations): self
    {
        return $this;
    }
    /**
     * observe
     */
    public static function observe(object|string $class): void
    {
        self::$observers[static::class] = is_string($class) ? new $class() : $class;
    }
    /**
     * paginate
     */
    public static function paginate(int $perPage = 15, int $page = 1): array
    {
        $total = static::count();
        $instance = new static();
        $conn = self::resolveConnection();
        $models = [];
        if ($conn !== null) {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$instance->table}";
            if ($instance->softDeletes) $sql .= " WHERE deleted_at IS NULL";
            $sql .= " LIMIT {$perPage} OFFSET {$offset}";
            $rows = $conn->select($sql);
            foreach ($rows as $row) {
                $m = new static($row);
                $models[] = $m->setExists(true)->syncOriginal();
            }
        }
        return [
            "data" => $models,
            "total" => $total,
            "per_page" => $perPage,
            "current_page" => $page,
            "last_page" => (int) ceil($total / max(1, $perPage)),
        ];
    }
    /**
     * query
     */
    public static function query(): array
    {
        return static::all();
    }
    /**
     * refresh
     */
    public function refresh(): self
    {
        $fresh = static::find($this->getKey());
        if ($fresh !== null) {
            $this->attributes = $fresh->getAttributes();
            $this->syncOriginal();
        }
        return $this;
    }
    /**
     * restore
     */
    public function restore(): self
    {
        $this->setAttribute("deleted_at", null); return $this->save();
    }
    /**
     * toJson
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
    /**
     * update
     */
    public function update(array $values): bool
    {
        $this->fill($values);
        return $this->save();
    }
    /**
     * updateOrCreate
     */
    public static function updateOrCreate(array $attributes, array $values = []): static
    {
        $model = static::firstOrCreate($attributes, $values);
        $model->fill($values)->save();
        return $model;
    }
    /**
     * with
     */
    public function with(array|string $relations): self
    {
        $clone = clone $this; return $clone;
    }
    /**
     * withCount
     */
    public function withCount(array|string $relations): self
    {
        $clone = clone $this; return $clone;
    }
}
