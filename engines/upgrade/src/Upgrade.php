<?php

declare(strict_types=1);

namespace Tavaan\Engine\Upgrade;

/**
 * DataPatch — کلاس پایه برای patch داده‌ای در ارتقا.
 */
abstract class DataPatch
{
    public string $version = '';
    public string $description = '';

    abstract public function up(): void;

    public function down(): void {}
}

/**
 * UpgradeStep — یک گام در مسیر ارتقا.
 */
final class UpgradeStep
{
    public function __construct(
        public readonly string $version,
        public readonly string $name,
        public readonly ?\Closure $up = null,
        public readonly ?\Closure $down = null,
    ) {
    }
}

/**
 * UpgradeManager — مدیریت ارتقا نسخه.
 *
 * @see docs/03-engines/upgrade.md (DOC-081E)
 */
final class UpgradeManager
{
    /** @var list<UpgradeStep> */
    private array $steps = [];

    private string $currentVersion = '1.0.0';

    /** @var list<string> */
    private array $executedSteps = [];

    private bool $backupCreated = false;

    private bool $maintenanceMode = false;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function addStep(UpgradeStep $step): self
    {
        $this->steps[] = $step;
        return $this;
    }

    public function setCurrentVersion(string $version): self
    {
        $this->currentVersion = $version;
        return $this;
    }

    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }

    public function preCheck(): bool
    {
        // بررسی PHP version، extensionها، فضا
        if (\PHP_VERSION_ID < 80200) return false;
        $diskFree = \disk_free_space(\sys_get_temp_dir());
        if ($diskFree !== false && $diskFree < 100 * 1024 * 1024) return false;
        return true;
    }

    public function backup(): self
    {
        // در عمل: backup DB + files
        $this->backupCreated = true;
        return $this;
    }

    public function isBackupCreated(): bool
    {
        return $this->backupCreated;
    }

    public function enableMaintenance(): self
    {
        $this->maintenanceMode = true;
        return $this;
    }

    public function disableMaintenance(): self
    {
        $this->maintenanceMode = false;
        return $this;
    }

    public function isMaintenanceEnabled(): bool
    {
        return $this->maintenanceMode;
    }

    /**
     * @return list<string> نسخه‌هایی که باید اجرا شوند
     */
    public function pendingSteps(string $targetVersion): array
    {
        $pending = [];
        foreach ($this->steps as $step) {
            if (\version_compare($step->version, $this->currentVersion, '>') &&
                \version_compare($step->version, $targetVersion, '<=')) {
                $pending[] = $step->version;
            }
        }
        return $pending;
    }

    /**
     * ارتقا به نسخهٔ هدف.
     *
     * @return array{success: bool, steps_run: int, version: string}
     */
    public function upgrade(string $targetVersion): array
    {
        if (!$this->preCheck()) {
            return ['success' => false, 'steps_run' => 0, 'version' => $this->currentVersion];
        }
        $this->backup();
        $this->enableMaintenance();
        $stepsRun = 0;
        foreach ($this->steps as $step) {
            if (\version_compare($step->version, $this->currentVersion, '>') &&
                \version_compare($step->version, $targetVersion, '<=')) {
                try {
                    if ($step->up !== null) ($step->up)();
                    $this->executedSteps[] = $step->version;
                    $this->currentVersion = $step->version;
                    $stepsRun++;
                } catch (\Throwable) {
                    $this->rollback();
                    return ['success' => false, 'steps_run' => $stepsRun, 'version' => $this->currentVersion];
                }
            }
        }
        $this->disableMaintenance();
        return ['success' => true, 'steps_run' => $stepsRun, 'version' => $this->currentVersion];
    }

    /**
     * Rollback از backup.
     */
    public function rollback(): void
    {
        // اجرای down() برای executed steps به ترتیب معکوس
        foreach (\array_reverse($this->executedSteps) as $version) {
            foreach ($this->steps as $step) {
                if ($step->version === $version && $step->down !== null) {
                    try { ($step->down)(); } catch (\Throwable) {}
                    break;
                }
            }
        }
        $this->executedSteps = [];
        $this->disableMaintenance();
    }

    /**
     * @return list<string>
     */
    public function getExecutedSteps(): array
    {
        return $this->executedSteps;
    }

    /** @return list<UpgradeStep> */
    public function allSteps(): array
    {
        return $this->steps;
    }
}

/**
 * UpgradeServiceProvider.
 */
final class UpgradeServiceProvider
{
    private ?UpgradeManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->manager = new UpgradeManager($this->config);
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [UpgradeManager::class, DataPatch::class];
    }

    public function getManager(): UpgradeManager
    {
        if ($this->manager === null) $this->register();
        return $this->manager;
    }
}
