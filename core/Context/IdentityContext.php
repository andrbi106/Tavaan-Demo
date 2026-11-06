<?php
declare(strict_types=1);
namespace Tavaan\Core\Context;

/**
 * Context هویت — user_id، tenant_id، roles.
 * برای auth/audit/logging.
 */
final class IdentityContext
{
    public function __construct(
        private ?int $userId = null,
        private ?int $tenantId = null,
        /** @var list<string> */
        private array $roles = [],
        private ?string $ipAddress = null,
    ) {}

    public function userId(): ?int { return $this->userId; }
    public function tenantId(): ?int { return $this->tenantId; }
    /** @return list<string> */
    public function roles(): array { return $this->roles; }
    public function ipAddress(): ?string { return $this->ipAddress; }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function isAuthenticated(): bool
    {
        return $this->userId !== null;
    }
}
