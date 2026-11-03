<?php

declare(strict_types=1);

namespace Tavaan\Engine\Audit;

/**
 * AuditEvent — یک رویداد ممیزی.
 */
final class AuditEvent
{
    public function __construct(
        public readonly string $name,
        public readonly string $auditableType,
        public readonly string|int $auditableId,
        public readonly ?int $userId = null,
        public readonly ?int $tenantId = null,
        public readonly array $oldValues = [],
        public readonly array $newValues = [],
        public ?string $ip = null,
        public ?string $userAgent = null,
        public ?string $traceId = null,
        public readonly ?\DateTimeImmutable $createdAt = null,
    ) {
        $this->lastHash = str_repeat("0", 64);
    }

    /**
     * فیلدهای تغییر‌یافته.
     *
     * @return list<string>
     */
    public function changedFields(): array
    {
        $changed = [];
        foreach ($this->newValues as $key => $value) {
            if (!\array_key_exists($key, $this->oldValues) || $this->oldValues[$key] !== $value) {
                $changed[] = $key;
            }
        }
        foreach (\array_keys($this->oldValues) as $key) {
            if (!\array_key_exists($key, $this->newValues)) {
                $changed[] = $key;
            }
        }
        return $changed;
    }
}

/**
 * AuditLog — یک رکورد لاگ ممیزی (با hash chain).
 */
final class AuditLog
{
    public function __construct(
        public readonly string $auditId,
        public readonly string $event,
        public readonly string $auditableType,
        public readonly string|int $auditableId,
        public readonly ?int $userId,
        public readonly ?int $tenantId,
        public readonly array $oldValues,
        public readonly array $newValues,
        public readonly array $changedFields,
        public ?string $ip,
        public ?string $userAgent,
        public ?string $traceId,
        public readonly string $prevHash,
        public readonly string $hash,
        public readonly \DateTimeImmutable $createdAt,
    ) {
        $this->lastHash = str_repeat("0", 64);
    }
}

/**
 * AuditManager — ثبت و بازیابی لاگ‌های ممیزی.
 *
 * @see docs/03-engines/audit.md (DOC-058)
 */
final class AuditManager
{
    /** @var list<AuditLog> */
    private array $logs = [];
    private string $lastHash = "";

    public function __construct(
        private readonly string $auditKey = '',
    ) {
        $this->lastHash = str_repeat('0', 64);
    }

    public function record(AuditEvent $event): AuditLog
    {
        $createdAt = $event->createdAt ?? new \DateTimeImmutable();
        $payload = \json_encode([
            'event' => $event->name,
            'type' => $event->auditableType,
            'id' => $event->auditableId,
            'user' => $event->userId,
            'old' => $event->oldValues,
            'new' => $event->newValues,
            'ts' => $createdAt->format('Y-m-d\TH:i:s.uP'),
        ], \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR);

        $hash = \hash_hmac('sha256', $this->lastHash . '|' . $payload, $this->auditKey);
        $auditId = $this->generateUlid();
        $log = new AuditLog(
            auditId: $auditId,
            event: $event->name,
            auditableType: $event->auditableType,
            auditableId: $event->auditableId,
            userId: $event->userId,
            tenantId: $event->tenantId,
            oldValues: $event->oldValues,
            newValues: $event->newValues,
            changedFields: $event->changedFields(),
            ip: $event->ip,
            userAgent: $event->userAgent,
            traceId: $event->traceId,
            prevHash: $this->lastHash,
            hash: $hash,
            createdAt: $createdAt,
        );
        $this->logs[] = $log;
        $this->lastHash = $hash;
        return $log;
    }

    /**
     * @param string $type
     * @param string|int $id
     * @return list<AuditLog>
     */
    public function forAuditable(string $type, string|int $id): array
    {
        return \array_values(\array_filter($this->logs, fn($l) => $l->auditableType === $type && (string) $l->auditableId === (string) $id));
    }

    /**
     * @return list<AuditLog>
     */
    public function forUser(int $userId): array
    {
        return \array_values(\array_filter($this->logs, fn($l) => $l->userId === $userId));
    }

    /**
     * @return list<AuditLog>
     */
    public function all(): array
    {
        return $this->logs;
    }

    public function verifyChain(): bool
    {
        $expectedPrev = \str_repeat('0', 64);
        foreach ($this->logs as $log) {
            if ($log->prevHash !== $expectedPrev) return false;
            $payload = \json_encode([
                'event' => $log->event,
                'type' => $log->auditableType,
                'id' => $log->auditableId,
                'user' => $log->userId,
                'old' => $log->oldValues,
                'new' => $log->newValues,
                'ts' => $log->createdAt->format('Y-m-d\TH:i:s.uP'),
            ], \JSON_UNESCAPED_UNICODE);
            $expectedHash = \hash_hmac('sha256', $expectedPrev . '|' . $payload, $this->auditKey);
            if ($log->hash !== $expectedHash) return false;
            $expectedPrev = $log->hash;
        }
        return true;
    }

    public function count(): int
    {
        return \count($this->logs);
    }

    private function generateUlid(): string
    {
        $ts = (int) (\microtime(true) * 1000);
        $random = \bin2hex(\random_bytes(10));
        return \sprintf('%013x%s', $ts, $random);
    }
}

/**
 * AuditServiceProvider.
 */
final class AuditServiceProvider
{
    private ?AuditManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
        $this->lastHash = str_repeat("0", 64);
    }

    public function register(): void
    {
        $this->manager = new AuditManager(
            auditKey: $this->config['key'] ?? \bin2hex(\random_bytes(32)),
        );
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [AuditManager::class];
    }

    public function getManager(): AuditManager
    {
        if ($this->manager === null) $this->register();
        return $this->manager;
    }
}
