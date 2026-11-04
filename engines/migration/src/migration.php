<?php
declare(strict_types=1);
namespace Tavaan\Engine\Database\Migration;

abstract class Migration
{
    public string $version = '';
    public string $description = '';
    abstract public function up(): void;
    public function down(): void {}
}

final class MigrationRepository
{
    private array $migrations = [];
    private array $ran = [];

    public function add(Migration $migration): void { $this->migrations[$migration->version] = $migration; }
    public function get(string $version): ?Migration { return $this->migrations[$version] ?? null; }
    public function all(): array { return $this->migrations; }
    public function pending(): array { return array_filter($this->migrations, fn($m) => !in_array($m->version, $this->ran, true)); }
    public function markRan(string $version): void { $this->ran[] = $version; }
    public function markRolledBack(string $version): void { $this->ran = array_values(array_diff($this->ran, [$version])); }
    public function getRan(): array { return $this->ran; }
    public function clear(): void { $this->ran = []; }
}

final class Migrator
{
    public function __construct(private readonly MigrationRepository $repo = new MigrationRepository()) {}

    public function run(): array
    {
        $executed = [];
        foreach ($this->repo->pending() as $migration) {
            $migration->up();
            $this->repo->markRan($migration->version);
            $executed[] = $migration->version;
        }
        return $executed;
    }

    public function rollback(int $steps = 1): array
    {
        $rolledBack = [];
        $ran = array_reverse($this->repo->getRan());
        foreach (array_slice($ran, 0, $steps) as $version) {
            $migration = $this->repo->get($version);
            if ($migration !== null) { $migration->down(); $this->repo->markRolledBack($version); $rolledBack[] = $version; }
        }
        return $rolledBack;
    }

    public function reset(): array
    {
        $all = array_reverse($this->repo->getRan());
        $rolledBack = [];
        foreach ($all as $version) {
            $migration = $this->repo->get($version);
            if ($migration !== null) { $migration->down(); $this->repo->markRolledBack($version); $rolledBack[] = $version; }
        }
        return $rolledBack;
    }

    public function refresh(): array { $this->reset(); return $this->run(); }
    public function status(): array { return ['ran' => $this->repo->getRan(), 'pending' => array_keys($this->repo->pending())]; }
    public function getRepository(): MigrationRepository { return $this->repo; }
}

final class MigrationServiceProvider
{
    private ?Migrator $migrator = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->migrator = new Migrator(); }
    public function boot(): void {}
    public function provides(): array { return [Migrator::class, MigrationRepository::class, Migration::class]; }
    public function getMigrator(): Migrator { if ($this->migrator === null) $this->register(); return $this->migrator; }

    /**
     * fresh — drop all migrations and re-run.
     */
    public function fresh(): array
    {
        $this->getMigrator()->getRepository()->clear();
        return $this->getMigrator()->run();
    }
    /**
     * install — register all migration files and run them.
     * @param array<int, Migration> $migrations
     */
    public function install(array $migrations = []): array
    {
        $migrator = $this->getMigrator();
        foreach ($migrations as $migration) {
            $migrator->getRepository()->add($migration);
        }
        return $migrator->run();
    }
}
