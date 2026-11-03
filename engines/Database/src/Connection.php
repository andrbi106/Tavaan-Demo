<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database;

/**
 * Connection — اتصال PDO با Query Log و Transaction Manager.
 *
 * @see docs/03-engines/database.md (DOC-047)
 */
final class Connection
{
    private ?\PDO $pdo = null;
    private int $transactionLevel = 0;

    /**
     * @var list<array{sql: string, bindings: array, time: float}>
     */
    private array $queryLog = [];

    public function __construct(
        private readonly string $dsn,
        private readonly ?string $username = null,
        private readonly ?string $password = null,
        private readonly array $options = [],
    ) {
    }

    public function getPdo(): \PDO
    {
        if ($this->pdo === null) {
            $defaultOptions = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new \PDO(
                $this->dsn,
                $this->username,
                $this->password,
                \array_merge($defaultOptions, $this->options)
            );
        }
        return $this->pdo;
    }

    /**
     * اجرای یک query با bindings.
     *
     * @param string $sql
     * @param array<int, mixed> $bindings
     * @return \PDOStatement
     */
    public function statement(string $sql, array $bindings = []): \PDOStatement
    {
        $start = \microtime(true);
        $stmt = $this->getPdo()->prepare($sql);
        foreach ($bindings as $i => $value) {
            $param = \is_int($i) ? $i + 1 : $i;
            $type = match (\gettype($value)) {
                'NULL' => \PDO::PARAM_NULL,
                'boolean' => \PDO::PARAM_BOOL,
                'integer' => \PDO::PARAM_INT,
                'double' => \PDO::PARAM_STR,
                'resource' => \PDO::PARAM_LOB,
                default => \PDO::PARAM_STR,
            };
            $stmt->bindValue($param, $value, $type);
        }
        $stmt->execute();
        $this->queryLog[] = [
            'sql' => $sql,
            'bindings' => $bindings,
            'time' => (float) \microtime(true) - $start,
        ];
        return $stmt;
    }

    /**
     * Select (returns array).
     *
     * @return list<array<string, mixed>>
     */
    public function select(string $sql, array $bindings = []): array
    {
        $stmt = $this->statement($sql, $bindings);
        return $stmt->fetchAll();
    }

    /**
     * Select first row.
     *
     * @return array<string, mixed>|null
     */
    public function selectOne(string $sql, array $bindings = []): ?array
    {
        $stmt = $this->statement($sql, $bindings);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function insert(string $sql, array $bindings = []): int
    {
        $this->statement($sql, $bindings);
        return (int) $this->getPdo()->lastInsertId();
    }

    public function update(string $sql, array $bindings = []): int
    {
        $stmt = $this->statement($sql, $bindings);
        return $stmt->rowCount();
    }

    public function delete(string $sql, array $bindings = []): int
    {
        $stmt = $this->statement($sql, $bindings);
        return $stmt->rowCount();
    }

    /**
     * اجرای query خالی (CREATE TABLE, ALTER, ...).
     */
    public function exec(string $sql): int
    {
        return (int) $this->getPdo()->exec($sql);
    }

    // === Transactions ===

    public function beginTransaction(): bool
    {
        if ($this->transactionLevel === 0) {
            $this->getPdo()->beginTransaction();
            $this->transactionLevel = 1;
        } else {
            // Savepoint for nested transactions
            $this->exec("SAVEPOINT sp{$this->transactionLevel}");
            $this->transactionLevel++;
        }
        return true;
    }

    public function commit(): bool
    {
        if ($this->transactionLevel === 0) return false;
        if ($this->transactionLevel === 1) {
            $this->getPdo()->commit();
            $this->transactionLevel = 0;
        } else {
            $this->transactionLevel--;
            $this->exec("RELEASE SAVEPOINT sp{$this->transactionLevel}");
        }
        return true;
    }

    public function rollBack(): bool
    {
        if ($this->transactionLevel === 0) return false;
        if ($this->transactionLevel === 1) {
            $this->getPdo()->rollBack();
            $this->transactionLevel = 0;
        } else {
            $this->transactionLevel--;
            $this->exec("ROLLBACK TO SAVEPOINT sp{$this->transactionLevel}");
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    public function getTransactionLevel(): int
    {
        return $this->transactionLevel;
    }

    // === Query Log ===

    public function getQueryLog(): array
    {
        return $this->queryLog;
    }

    public function flushQueryLog(): void
    {
        $this->queryLog = [];
    }

    public function disconnect(): void
    {
        $this->pdo = null;
    }

    /**
     * affectingStatement
     */
    public function affectingStatement(string $sql, array $bindings = []): int
    {
        return $this->exec($sql);
    }
    /**
     * cursor
     */
    public function cursor(string $sql, array $bindings = []): \Generator
    {
        $stmt = $this->statement($sql, $bindings);
        while ($row = $stmt->fetch()) {
            yield $row;
        }
    }
    /**
     * lastInsertId
     */
    public function lastInsertId(?string $name = null): string
    {
        return (string) $this->getPdo()->lastInsertId($name);
    }
    /**
     * pdo
     */
    public function pdo(): \PDO
    {
        return $this->getPdo();
    }
    /**
     * queryLog
     */
    public function queryLog(): array
    {
        return $this->queryLog;
    }
    /**
     * transactionLevel
     */
    public function transactionLevel(): int
    {
        return $this->transactionLevel;
    }
}
