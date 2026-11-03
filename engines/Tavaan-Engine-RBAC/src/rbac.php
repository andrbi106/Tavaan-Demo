<?php
declare(strict_types=1);
namespace Tavaan\Engine\Rbac;

final class Role
{
    public function __construct(public readonly string $name, public readonly array $permissions = []) {}
    public function hasPermission(string $permission): bool { return in_array($permission, $this->permissions, true) || in_array('*', $this->permissions, true); }
}

final class RbacManager
{
    private array $roles = [];
    private array $userRoles = [];

    public function __construct(private readonly array $config = [])
    {
        foreach ($config['roles'] ?? [] as $name => $perms) { $this->roles[$name] = new Role($name, $perms); }
    }

    public function addRole(string $name, array $permissions = []): self { $this->roles[$name] = new Role($name, $permissions); return $this; }
    public function getRole(string $name): ?Role { return $this->roles[$name] ?? null; }
    public function hasRole(string $name): bool { return isset($this->roles[$name]); }

    public function assignRole(mixed $userId, string $role): void { $this->userRoles[$userId][] = $role; }
    public function revokeRole(mixed $userId, string $role): void { if (isset($this->userRoles[$userId])) { $this->userRoles[$userId] = array_values(array_diff($this->userRoles[$userId], [$role])); } }

    public function hasPermission(mixed $userId, string $permission): bool
    {
        $roles = $this->userRoles[$userId] ?? [];
        foreach ($roles as $roleName) {
            $role = $this->roles[$roleName] ?? null;
            if ($role !== null && $role->hasPermission($permission)) return true;
        }
        return false;
    }

    public function can(mixed $userId, string $permission): bool { return $this->hasPermission($userId, $permission); }
    public function cannot(mixed $userId, string $permission): bool { return !$this->can($userId, $permission); }

    public function getUserRoles(mixed $userId): array { return $this->userRoles[$userId] ?? []; }
    public function getAllRoles(): array { return array_keys($this->roles); }
}

final class RbacServiceProvider
{
    private ?RbacManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new RbacManager($this->config); }
    public function boot(): void {}
    public function provides(): array { return [RbacManager::class, Role::class]; }
    public function getManager(): RbacManager { if ($this->manager === null) $this->register(); return $this->manager; }
}
