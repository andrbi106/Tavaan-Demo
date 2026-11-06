<?php

declare(strict_types=1);

namespace Tavaan\Engine\Upgrade;

/**
 * Upgrader — patch-based version upgrader.
 */
final class Upgrader
{
    /** @var array<string, Patch> */
    private array $patches = [];
    /** @var list<string> */
    private array $applied = [];

    /** @param array<string, mixed> $config */
    public function __construct(private readonly array $config = []) {}

    /**
     * register a patch.
     */
    public function add(Patch $patch): void
    {
        $this->patches[$patch->version] = $patch;
    }

    /**
     * upgrade — apply all pending patches from `fromVersion` to `toVersion`.
     * @return list<string> applied patch versions
     */
    public function upgrade(?string $fromVersion = null, ?string $toVersion = null): array
    {
        $applied = [];
        $sorted = $this->patches;
        ksort($sorted);
        foreach ($sorted as $version => $patch) {
            if ($fromVersion !== null && version_compare($version, $fromVersion, '<=')) {
                continue;
            }
            if ($toVersion !== null && version_compare($version, $toVersion, '>')) {
                continue;
            }
            if (in_array($version, $this->applied, true)) { continue; }
            $patch->up();
            $this->applied[] = $version;
            $applied[] = $version;
        }
        return $applied;
    }

    /**
     * check — is the manager initialised? (placeholder check)
     */
    public function check(): bool
    {
        return !empty($this->patches);
    }

    /**
     * rollback — roll back N most recently applied patches.
     * @return list<string> rolled-back patch versions
     */
    public function rollback(int $steps = 1): array
    {
        $rolled = [];
        foreach (array_slice(array_reverse($this->applied), 0, $steps) as $version) {
            $patch = $this->patches[$version] ?? null;
            if ($patch !== null) {
                $patch->down();
                $rolled[] = $version;
            }
        }
        $this->applied = array_values(array_diff($this->applied, $rolled));
        return $rolled;
    }

    /**
     * status — return current upgrade state.
     * @return array{applied: list<string>, pending: list<string>}
     */
    public function status(): array
    {
        $all = array_keys($this->patches);
        return [
            'applied' => $this->applied,
            'pending' => array_values(array_diff($all, $this->applied)),
        ];
    }

    /**
     * patches — return list of registered patch versions.
     * @return list<string>
     */
    public function patches(): array
    {
        return array_keys($this->patches);
    }
}

/**
 * Patch — a single upgradable/downgradable patch.
 */
abstract class Patch
{
    public function __construct(
        public readonly string $version,
        public readonly string $description = '',
    ) {}

    abstract public function up(): void;
    abstract public function down(): void;
}
