<?php

declare(strict_types=1);

namespace Tavaan\Engine\Database;

/**
 * ConnectionManager — مدیریت چند اتصال (default, tenant, reporting).
 *
 * @see docs/03-engines/database.md
 */
final class ConnectionManager
{
    /**
     * @var array<string, Connection>
     */
    private array $connections = [];

    /**
     * @param array<string, array<string, mixed>> $config connections config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function connection(?string $name = null): Connection
    {
        $name ??= $this->config['default'] ?? 'default';
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->createConnection($name);
        }
        return $this->connections[$name];
    }

    private function createConnection(string $name): Connection
    {
        $cfg = $this->config['connections'][$name] ?? null;
        if ($cfg === null) {
            throw new Exceptions\DatabaseException("Connection '{$name}' not configured.");
        }
        $options = [];
        if (isset($cfg['options'])) {
            foreach ($cfg['options'] as $k => $v) {
                $options[\constant($k)] = $v;
            }
        }
        return new Connection(
            dsn: $cfg['dsn'] ?? $this->buildDsn($cfg),
            username: $cfg['username'] ?? null,
            password: $cfg['password'] ?? null,
            options: $options,
        );
    }

    /**
     * @param array<string, mixed> $cfg
     */
    private function buildDsn(array $cfg): string
    {
        $driver = $cfg['driver'] ?? 'sqlite';
        return match ($driver) {
            'sqlite' => "sqlite:{$cfg['database']}",
            'mysql' => \sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $cfg['host'] ?? '127.0.0.1',
                $cfg['port'] ?? 3306,
                $cfg['database'] ?? '',
                $cfg['charset'] ?? 'utf8mb4'),
            'pgsql' => \sprintf('pgsql:host=%s;port=%d;dbname=%s',
                $cfg['host'] ?? '127.0.0.1',
                $cfg['port'] ?? 5432,
                $cfg['database'] ?? ''),
            default => $cfg['dsn'] ?? '',
        };
    }

    public function getDefaultConnection(): string
    {
        return $this->config['default'] ?? 'default';
    }

    public function addConnection(string $name, array $config): self
    {
        $this->config['connections'][$name] = $config;
        return $this;
    }

    public function disconnect(?string $name = null): void
    {
        if ($name !== null) {
            $this->connections[$name]?->disconnect();
            unset($this->connections[$name]);
            return;
        }
        foreach ($this->connections as $c) $c->disconnect();
        $this->connections = [];
    }
}
