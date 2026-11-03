<?php

declare(strict_types=1);

namespace Tavaan\Engine\Rbac;

final class RoleManager
{
    private array $roles = [];
    private array $userRoles = [];
    private array $hierarchy = [];

    public function __construct(private readonly array $config = [])
    {
        foreach ($config['roles'] ?? [] as $name => $permissions) {
            $this->roles[$name] = new Role($name, $permissions);
        }
    }

    public function create(array $data): self
    {
        $name = $data['name'] ?? '';
        $permissions = $data['permissions'] ?? [];
        $this->roles[$name] = new Role($name, $permissions);
        return $this;
    }

    public function assign(mixed $userId, string $role): self
    {
        if (!isset($this->userRoles[$userId])) {
            $this->userRoles[$userId] = [];
        }
        if (!in_array($role, $this->userRoles[$userId], true)) {
            $this->userRoles[$userId][] = $role;
        }
        return $this;
    }

    public function remove(string $key): self
    {
        unset($this->roles[$key]);
        foreach ($this->userRoles as $userId => $roles) {
            $this->userRoles[$userId] = array_values(array_diff($roles, [$key]));
        }
        return $this;
    }

    public function hierarchy(string $parent, string $child): self
    {
        $this->hierarchy[$parent][] = $child;
        return $this;
    }

    public function syncFromModules(array $modules): self
    {
        foreach ($modules as $module) {
            $name = $module['name'] ?? '';
            $permissions = $module['permissions'] ?? [];
            if ($name !== '') {
                $this->roles[$name] = new Role($name, $permissions);
            }
        }
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserRoles(mixed $userId): array
    {
        return $this->userRoles[$userId] ?? [];
    }

    public function hasPermission(mixed $userId, string $permission): bool
    {
        $roles = $this->getUserRoles($userId);
        foreach ($roles as $roleName) {
            $role = $this->roles[$roleName] ?? null;
            if ($role !== null && $role->hasPermission($permission)) return true;
            // Check hierarchy
            foreach ($this->hierarchy[$roleName] ?? [] as $child) {
                $childRole = $this->roles[$child] ?? null;
                if ($childRole !== null && $childRole->hasPermission($permission)) return true;
            }
        }
        return false;
    }
}
