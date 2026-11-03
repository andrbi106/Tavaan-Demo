<?php

declare(strict_types=1);

namespace Tavaan\Engine\Admin\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Admin\Form;
use Tavaan\Engine\Admin\Resource;
use Tavaan\Engine\Admin\AdminManager;

/**
 * T-NN scenario tests for the admin engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class AdminScenarioTest extends TestCase
{
    public function test_t01_form_text_field_renders_input(): void
    {
        // Scenario T-01
$f = new Form();
        $html = $f->text("name", "Name");
        $this->assertStringContainsString("<input", $html);
        $this->assertStringContainsString("type=\"text\"", $html);
    }
    public function test_t02_form_select_field_renders_options(): void
    {
        // Scenario T-02
$f = new Form();
        $html = $f->select("role", ["admin" => "Admin", "user" => "User"], "Role");
        $this->assertStringContainsString("<select", $html);
        $this->assertStringContainsString("Admin", $html);
        $this->assertStringContainsString("User", $html);
    }
    public function test_t03_resource_columns_and_searchable(): void
    {
        // Scenario T-03
$r = new Resource("posts", "PostController");
        $r->columns(["id", "title"])->searchable(["title"])->sortable(["id"]);
        $this->assertSame("Posts", $r->title());
        $this->assertContains("title", $r->getSearchable());
    }
    public function test_t04_admin_manager_menu(): void
    {
        // Scenario T-04
$mgr = new AdminManager();
        $mgr->addMenuItem("content", "Posts", "/admin/posts");
        $mgr->addMenuItem("content", "Pages", "/admin/pages");
        $menu = $mgr->getMenu();
        $this->assertCount(2, $menu);
        $this->assertSame("Posts", $menu[0]["title"]);
    }
}
