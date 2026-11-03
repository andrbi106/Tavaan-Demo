<?php
declare(strict_types=1);
namespace Tavaan\Engine\Backup;

final class Backup
{
    public function __construct(
        public readonly string $name,
        public readonly string $path,
        public readonly int $size,
        public readonly \DateTimeImmutable $createdAt,
        public readonly string $type = 'full',
    ) {}
}

final class BackupManager
{
    private array $backups = [];

    public function __construct(private array $config = [])
    {
        $this->config['backup_dir'] ??= 'storage/backups';
    }

    public function create(string $name, string $type = 'full'): Backup
    {
        $backupDir = $this->config['backup_dir'] ?? 'storage/backups';
        $timestamp = date('Y_m_d_His');
        $path = "{$backupDir}/{$name}_{$timestamp}.bak";
        $content = json_encode(['name' => $name, 'type' => $type, 'timestamp' => $timestamp, 'data' => 'backup_content_placeholder']);
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0777, true);
        file_put_contents($path, $content);
        $backup = new Backup($name, $path, strlen($content), new \DateTimeImmutable(), $type);
        $this->backups[] = $backup;
        return $backup;
    }

    public function restore(string $path): bool
    {
        if (!file_exists($path)) return false;
        $content = file_get_contents($path);
        if ($content === false) return false;
        $data = json_decode($content, true);
        return is_array($data);
    }

    public function delete(string $path): bool
    {
        if (!file_exists($path)) return false;
        return unlink($path);
    }

    public function list(): array { return $this->backups; }
    public function count(): int { return count($this->backups); }
    public function clear(): void { $this->backups = []; }
}

final class BackupServiceProvider
{
    private ?BackupManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new BackupManager($this->config); }
    public function boot(): void {}
    public function provides(): array { return [BackupManager::class, Backup::class]; }
    public function getManager(): BackupManager { if ($this->manager === null) $this->register(); return $this->manager; }

    public function database(string $name = 'default'): Backup
    {
        $dumpPath = $this->getManager()->create('db_' . $name, 'database')->path;
        // For an actual dump, you would shell out to pg_dump / mysqldump here.
        // We write a placeholder dump and register the Backup.
        return new Backup('db_' . $name, $dumpPath, 0, new \DateTimeImmutable(), 'database');
    }
    public function files(string $dir = null): array
    {
        $target = $dir ?? ($this->config['backup_dir'] ?? 'storage/backups');
        if (!is_dir($target)) { return []; }
        $all = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target));
        foreach ($iterator as $file) {
            if ($file->isFile()) { $all[] = $file->getPathname(); }
        }
        return $all;
    }
    public function prune(int $keepDays = 7): int
    {
        $manager = $this->getManager();
        $deleted = 0;
        $cutoff = new \DateTimeImmutable("-{$keepDays} days");
        foreach ($manager->list() as $backup) {
            if ($backup->createdAt < $cutoff) {
                $manager->delete($backup->path);
                $deleted++;
            }
        }
        return $deleted;
    }
    public function run(string $name = 'full'): Backup
    {
        return $this->getManager()->create($name, $name);
    }
    public function verify(string $path): bool
    {
        if (!file_exists($path)) { return false; }
        $content = @file_get_contents($path);
        if ($content === false) { return false; }
        $data = json_decode($content, true);
        return is_array($data) && isset($data['name'], $data['timestamp']);
    }
}
