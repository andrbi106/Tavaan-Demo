<?php

declare(strict_types=1);

namespace Tavaan\Engine\Rbac\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Rbac\RbacManager;

/**
 * Feature tests for the rbac engine.
 */
final class RbacFeatureTest extends TestCase
{
    public function test_add_role_returns_self(): void
    {
$mgr = new RbacManager();
        $ret = $mgr->addRole("admin", ["delete", "create"]);
        $this->assertSame($mgr, $ret);
        $this->assertTrue($mgr->hasRole("admin"));
    }

    public function test_assign_role_to_user(): void
    {
$mgr = new RbacManager();
        $mgr->addRole("admin", ["delete"]);
        $mgr->assignRole(1, "admin");
        $this->assertContains("admin", $mgr->getUserRoles(1));
    }

    public function test_user_without_role_returns_empty(): void
    {
$mgr = new RbacManager();
        $mgr->addRole("admin", ["delete"]);
        $this->assertSame([], $mgr->getUserRoles(99));
    }

    public function test_check_permission_via_role(): void
    {
$mgr = new RbacManager();
        $mgr->addRole("admin", ["delete"]);
        $mgr->assignRole(1, "admin");
        $this->assertTrue($mgr->hasPermission(1, "delete"));
        $this->assertTrue($mgr->can(1, "delete"));
    }

    public function test_check_permission_without_role_returns_false(): void
    {
$mgr = new RbacManager();
        $mgr->addRole("admin", ["delete"]);
        $this->assertFalse($mgr->hasPermission(5, "delete"));
        $this->assertTrue($mgr->cannot(5, "delete"));
    }

    public function test_revoke_role_from_user(): void
    {
$mgr = new RbacManager();
        $mgr->addRole("admin", ["delete"]);
        $mgr->assignRole(1, "admin");
        $mgr->revokeRole(1, "admin");
        $this->assertSame([], $mgr->getUserRoles(1));
    }

    public function test_get_all_roles(): void
    {
$mgr = new RbacManager();
        $mgr->addRole("admin");
        $mgr->addRole("editor");
        $this->assertSame(["admin", "editor"], $mgr->getAllRoles());
    }

    public function test_wildcard_permission_grants_all(): void
    {
$mgr = new RbacManager();
        $mgr->addRole("super", ["*"]);
        $mgr->assignRole(1, "super");
        $this->assertTrue($mgr->hasPermission(1, "anything"));
    }
}
