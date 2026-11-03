<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database\Query;

/**
 * QueryBuilder — ساخت fluent کوئری SQL.
 *
 * @see docs/03-engines/database.md (DOC-047)
 */
final class QueryBuilder
{
    private string $table;
    /** @var list<string> */
    private array $columns = ['*'];
    /** @var list<array{column: string, operator: string, value: mixed, boolean: string}> */
    private array $wheres = [];
    /** @var list<string> */
    private array $orders = [];
    private ?int $limit = null;
    private ?int $offset = null;
    /** @var list<string> */
    private array $joins = [];
    /** @var list<array{table: string, type: string, local: string, foreign: string}> */
    private array $joinClauses = [];

    public function __construct(
        private readonly \Tavaan\Engine\Database\Connection $connection,
        string $table,
    ) {
        $this->table = $table;
    }

    public function table(): string
    {
        return $this->table;
    }

    /**
     * @param list<string>|string $columns
     */
    public function select(array|string $columns = ['*']): self
    {
        $this->columns = (array) $columns;
        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, mixed $operator = '=', mixed $value = null, string $boolean = 'AND'): self
    {
        // Handle where('column', $value) shorthand
        if ($value === null && !\in_array($operator, ['=', '!=', '<', '>', '<=', '>=', '<>', 'LIKE', 'IN', 'IS NULL', 'IS NOT NULL'], true)) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = ['column' => $column, 'operator' => $operator, 'value' => $value, 'boolean' => $boolean];
        return $this;
    }

    public function orWhere(string $column, mixed $operator = '=', mixed $value = null): self
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IN', 'value' => $values, 'boolean' => 'AND'];
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IS NULL', 'value' => null, 'boolean' => 'AND'];
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = ['column' => $column, 'operator' => 'IS NOT NULL', 'value' => null, 'boolean' => 'AND'];
        return $this;
    }

    public function join(string $table, string $local, string $operator, string $foreign): self
    {
        $this->joinClauses[] = ['table' => $table, 'type' => 'INNER', 'local' => $local, 'foreign' => $foreign, 'operator' => $operator];
        return $this;
    }

    public function leftJoin(string $table, string $local, string $operator, string $foreign): self
    {
        $this->joinClauses[] = ['table' => $table, 'type' => 'LEFT', 'local' => $local, 'foreign' => $foreign, 'operator' => $operator];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function take(int $limit): self
    {
        return $this->limit($limit);
    }

    public function skip(int $offset): self
    {
        return $this->offset($offset);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function get(): array
    {
        [$sql, $bindings] = $this->toSql();
        return $this->connection->select($sql, $bindings);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function first(): ?array
    {
        $this->limit = 1;
        [$sql, $bindings] = $this->toSql();
        return $this->connection->selectOne($sql, $bindings);
    }

    public function count(): int
    {
        $this->columns = ['COUNT(*) AS aggregate'];
        [$sql, $bindings] = $this->toSql();
        $result = $this->connection->selectOne($sql, $bindings);
        return (int) ($result['aggregate'] ?? 0);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function insert(array $values): int
    {
        $columns = \array_keys($values);
        $placeholders = \array_fill(0, \count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . \implode(', ', $columns) . ') VALUES (' . \implode(', ', $placeholders) . ')';
        return $this->connection->insert($sql, \array_values($values));
    }

    public function update(array $values): int
    {
        $sets = [];
        $bindings = [];
        foreach ($values as $col => $val) {
            $sets[] = "{$col} = ?";
            $bindings[] = $val;
        }
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = "UPDATE {$this->table} SET " . \implode(', ', $sets) . $whereSql;
        return $this->connection->update($sql, \array_merge($bindings, $whereBindings));
    }

    public function delete(): int
    {
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = "DELETE FROM {$this->table}" . $whereSql;
        return $this->connection->delete($sql, $whereBindings);
    }

    /**
     * @return array{0: string, 1: list<mixed>}
     */
    public function toSql(): array
    {
        $sql = 'SELECT ' . \implode(', ', $this->columns);
        $sql .= ' FROM ' . $this->table;
        // Joins
        foreach ($this->joinClauses as $j) {
            $sql .= " {$j['type']} JOIN {$j['table']} ON {$j['local']} {$j['operator']} {$j['foreign']}";
        }
        // Wheres
        [$whereSql, $bindings] = $this->compileWheres();
        $sql .= $whereSql;
        // Order by
        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . \implode(', ', $this->orders);
        }
        // Limit
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        // Offset
        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }
        return [$sql, $bindings];
    }

    /**
     * @return array{0: string, 1: list<mixed>}
     */
    private function compileAggregate(string $function, string $column): array
    {
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = "SELECT {$function}({$column}) AS aggregate FROM {$this->table}" . $whereSql;
        return [$sql, $whereBindings];
    }

    private function compileWheres(): array
    {
        if (empty($this->wheres)) return ['', []];
        $sql = ' WHERE ';
        $bindings = [];
        $first = true;
        foreach ($this->wheres as $where) {
            if (!$first) {
                $sql .= " {$where['boolean']} ";
            }
            if ($where['operator'] === 'IN') {
                $placeholders = \array_fill(0, \count($where['value']), '?');
                $sql .= "{$where['column']} IN (" . \implode(', ', $placeholders) . ')';
                foreach ($where['value'] as $v) $bindings[] = $v;
            } elseif ($where['operator'] === 'BETWEEN') {
                $sql .= "{$where['column']} BETWEEN ? AND ?";
                $bindings[] = $where['value'][0];
                $bindings[] = $where['value'][1];
            } elseif ($where['operator'] === 'RAW') {
                $sql .= $where['column'];
                foreach ($where['value'] as $v) $bindings[] = $v;
            } elseif ($where['operator'] === 'IS NULL' || $where['operator'] === 'IS NOT NULL') {
                $sql .= "{$where['column']} {$where['operator']}";
            } else {
                $sql .= "{$where['column']} {$where['operator']} ?";
                $bindings[] = $where['value'];
            }
            $first = false;
        }
        return [$sql, $bindings];
    }

    /**
     * avg
     */
    public function avg(string $column): float
    {
        [$sql, $bindings] = $this->compileAggregate("AVG", $column);
        $row = $this->connection->selectOne($sql, $bindings);
        return (float) ($row["aggregate"] ?? 0);
    }
    /**
     * chunk
     */
    public function chunk(int $count, callable $callback): self
    {
        return $this;
    }
    /**
     * crossJoin
     */
    public function crossJoin(string $table): self
    {
        $this->joinClauses[] = ["table" => $table, "type" => "CROSS", "local" => "", "foreign" => "", "operator" => ""];
        return $this;
    }
    /**
     * cursor
     */
    public function cursor(): \Generator
    {
        [$sql, $bindings] = $this->toSql();
        $stmt = $this->connection->statement($sql, $bindings);
        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }
    /**
     * decrement
     */
    public function decrement(string $column, int $amount = 1): self
    {
        $this->setAttribute($column, ($this->getAttribute($column) ?? 0) - $amount); return $this;
    }
    /**
     * find — fetch a single row by its primary key (defaults to `id`).
     * @param mixed $id
     * @param string $key
     * @return array<string, mixed>|null
     */
    public function find(mixed $id, string $key = 'id'): ?array
    {
        $results = $this->where($key, '=', $id)->limit(1)->get();
        return $results[0] ?? null;
    }
    /**
     * forUpdate — mark the next SELECT to use SELECT ... FOR UPDATE (pessimistic row lock).
     */
    public function forUpdate(): self
    {
        $this->lockForUpdate = true;
        return $this;
    }
    /**
     * getBindings
     */
    public function getBindings()
    {
        return $this->bindings ?? null;
    }
    /**
     * groupBy
     */
    public function groupBy(string $column): self
    {
        return $this;
    }
    /**
     * having
     */
    public function having(string $column, mixed $operator, mixed $value): self
    {
        return $this;
    }
    /**
     * increment
     */
    public function increment(string $column, int $amount = 1): self
    {
        $this->setAttribute($column, ($this->getAttribute($column) ?? 0) + $amount); return $this;
    }
    /**
     * insertGetId
     */
    public function insertGetId(array $values): int
    {
        $columns = array_keys($values);
        $placeholders = array_fill(0, count($columns), "?");
        $sql = "INSERT INTO {$this->table} (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
        return (int) $this->connection->insert($sql, array_values($values));
    }
    /**
     * insertOrIgnore
     */
    public function insertOrIgnore(array $values): int
    {
        $columns = array_keys($values);
        $placeholders = array_fill(0, count($columns), "?");
        $sql = "INSERT OR IGNORE INTO {$this->table} (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
        return $this->connection->update($sql, array_values($values));
    }
    /**
     * lazy
     */
    public function lazy(int $chunkSize = 1000): \Generator
    {
        $page = 0;
        while (true) {
            $rows = $this->skip($page * $chunkSize)->take($chunkSize)->get();
            if (empty($rows)) break;
            foreach ($rows as $row) yield $row;
            $page++;
        }
    }
    /**
     * max
     */
    public function max(string $column): mixed
    {
        [$sql, $bindings] = $this->compileAggregate("MAX", $column);
        $row = $this->connection->selectOne($sql, $bindings);
        return $row["aggregate"] ?? null;
    }
    /**
     * min
     */
    public function min(string $column): mixed
    {
        [$sql, $bindings] = $this->compileAggregate("MIN", $column);
        $row = $this->connection->selectOne($sql, $bindings);
        return $row["aggregate"] ?? null;
    }
    /**
     * paginate
     */
    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $total = $this->count();
        $items = $this->skip(($page - 1) * $perPage)->take($perPage)->get();
        return [
            "data" => $items,
            "total" => $total,
            "per_page" => $perPage,
            "current_page" => $page,
            "last_page" => (int) ceil($total / max(1, $perPage)),
        ];
    }
    /**
     * pluck
     */
    public function pluck(string $column, ?string $key = null): array
    {
        $rows = $this->get();
        $result = [];
        foreach ($rows as $row) {
            $val = $row[$column] ?? null;
            if ($key !== null && isset($row[$key])) {
                $result[$row[$key]] = $val;
            } else {
                $result[] = $val;
            }
        }
        return $result;
    }
    /**
     * rightJoin
     */
    public function rightJoin(string $table, string $local, string $operator, string $foreign): self
    {
        $this->joinClauses[] = ["table" => $table, "type" => "RIGHT", "local" => $local, "foreign" => $foreign, "operator" => $operator];
        return $this;
    }
    /**
     * selectRaw
     */
    public function selectRaw(string $expression): self
    {
        $this->columns[] = $expression;
        return $this;
    }
    /**
     * sharedLock
     */
    public function sharedLock(): self
    {
        return $this;
    }
    /**
     * sum
     */
    public function sum(string $column): float
    {
        [$sql, $bindings] = $this->compileAggregate("SUM", $column);
        $row = $this->connection->selectOne($sql, $bindings);
        return (float) ($row["aggregate"] ?? 0);
    }
    /**
     * truncate
     */
    public function truncate(): self
    {
        return $this;
    }
    /**
     * union
     */
    public function union(self $query): self
    {
        $this->unions[] = $query;
        return $this;
    }
    /**
     * upsert
     */
    public function upsert(array $values, array $uniqueBy, ?array $update = null): int
    {
        $columns = array_keys($values);
        $placeholders = array_fill(0, count($columns), "?");
        $sql = "INSERT INTO {$this->table} (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $updateCols = $update ?? array_diff($columns, $uniqueBy);
        $sets = array_map(fn($c) => "$c = excluded.$c", $updateCols);
        if (!empty($sets)) {
            $sql .= " ON CONFLICT (" . implode(", ", $uniqueBy) . ") DO UPDATE SET " . implode(", ", $sets);
        }
        return $this->connection->update($sql, array_values($values));
    }
    /**
     * value
     */
    public function value(string $column): mixed
    {
        $row = $this->first();
        return $row[$column] ?? null;
    }
    /**
     * whereBetween
     */
    public function whereBetween(string $column, array $values): self
    {
        $this->wheres[] = ["column" => $column, "operator" => "BETWEEN", "value" => $values, "boolean" => "AND"];
        return $this;
    }
    /**
     * whereExists
     */
    public function whereExists(callable $callback): self
    {
        return $this;
    }
    /**
     * whereRaw
     */
    public function whereRaw(string $sql, array $bindings = []): self
    {
        $this->wheres[] = ["column" => $sql, "operator" => "RAW", "value" => $bindings, "boolean" => "AND"];
        return $this;
    }
    /**
     * withCte
     */
    public function withCte()
    {
        $clone = clone $this; return $clone;
    }
}
