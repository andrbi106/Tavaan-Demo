<?php

declare(strict_types=1);

namespace Tavaan\Engine\Rbac\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Rbac\RbacManager;

/**
 * T-NN scenario tests for the rbac engine (real implementations).
 */
final class RbacScenarioTest extends TestCase
{
    public function test_t01_assign_role_and_check(): void
    {
        // Scenario T-01
$mgr = new RbacManager();
        $mgr->addRole("admin", ["delete"]);
        $mgr->assignRole(1, "admin");
        $this->assertContains("admin", $mgr->getUserRoles(1));
        $this->assertTrue($mgr->hasPermission(1, "delete"));
    }

    public function test_t02_permission_via_role(): void
    {
        // Scenario T-02
$mgr = new RbacManager();
        $mgr->addRole("editor", ["edit"]);
        $mgr->assignRole(1, "editor");
        $this->assertTrue($mgr->can(1, "edit"));
        $this->assertTrue($mgr->cannot(1, "delete"));
    }

    public function test_t03_revoke_role_removes_permission(): void
    {
        // Scenario T-03
$mgr = new RbacManager();
        $mgr->addRole("admin", ["delete"]);
        $mgr->assignRole(1, "admin");
        $this->assertTrue($mgr->hasPermission(1, "delete"));
        $mgr->revokeRole(1, "admin");
        $this->assertFalse($mgr->hasPermission(1, "delete"));
    }

    public function test_t04_wildcard_permission(): void
    {
        // Scenario T-04
$mgr = new RbacManager();
        $mgr->addRole("super", ["*"]);
        $mgr->assignRole(1, "super");
        $this->assertTrue($mgr->hasPermission(1, "anything"));
        $this->assertTrue($mgr->hasPermission(1, "another-perm"));
    }
}
