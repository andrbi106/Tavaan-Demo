<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\ORM;

/**
 * Repository — کلاس پایه برای repository pattern.
 *
 * @template T of Model
 */
abstract class Repository
{
    /**
     * @param class-string<T> $modelClass
     */
    public function __construct(
        private readonly string $modelClass,
    ) {
    }

    public function find(int|string $id): ?Model
    {
        return ($this->modelClass)::find($id);
    }

    public function all(): array
    {
        return ($this->modelClass)::all();
    }

    public function where(string $column, mixed $operator = '=', mixed $value = null): array
    {
        return ($this->modelClass)::where($column, $operator, $value);
    }

    public function create(array $attributes): ?Model
    {
        return ($this->modelClass)::create($attributes);
    }

    public function count(): int
    {
        return ($this->modelClass)::count();
    }

    public function save(Model $model): bool
    {
        return $model->save();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
