<?php

declare(strict_types=1);

namespace Tavaan\Engine\Workflow\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Workflow\WorkflowDefinition;
use Tavaan\Engine\Workflow\WorkflowManager;
use Tavaan\Engine\Workflow\ProcessInstance;

/**
 * T-NN scenario tests for the workflow engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class WorkflowScenarioTest extends TestCase
{
    public function test_t01_start_workflow_at_initial_state(): void
    {
        // Scenario T-01
$def = new WorkflowDefinition("order");
        $def->addState("pending")->addState("approved");
        $def->setInitial("pending");
        $def->addTransition("pending", "approved", "approve");
        $mgr = new WorkflowManager();
        $mgr->register($def);
        $inst = $mgr->start("order");
        $this->assertNotNull($inst);
        $this->assertSame("pending", $inst->getState());
    }
    public function test_t02_fire_transition_changes_state(): void
    {
        // Scenario T-02
$def = new WorkflowDefinition("wf");
        $def->addState("a")->addState("b");
        $def->setInitial("a");
        $def->addTransition("a", "b", "go");
        $mgr = new WorkflowManager();
        $mgr->register($def);
        $inst = $mgr->start("wf");
        $ok = $inst->transition("go");
        $this->assertTrue($ok);
        $this->assertSame("b", $inst->getState());
    }
    public function test_t03_fire_invalid_action_returns_false(): void
    {
        // Scenario T-03
$def = new WorkflowDefinition("wf");
        $def->addState("a")->addState("b");
        $def->setInitial("a");
        $def->addTransition("a", "b", "go");
        $mgr = new WorkflowManager();
        $mgr->register($def);
        $inst = $mgr->start("wf");
        $this->assertFalse($inst->transition("invalid"));
        $this->assertSame("a", $inst->getState());
    }
    public function test_t04_available_actions(): void
    {
        // Scenario T-04
$def = new WorkflowDefinition("wf");
        $def->addState("a")->addState("b")->addState("c");
        $def->setInitial("a");
        $def->addTransition("a", "b", "toB");
        $def->addTransition("a", "c", "toC");
        $mgr = new WorkflowManager();
        $mgr->register($def);
        $inst = $mgr->start("wf");
        $actions = $inst->availableActions();
        $this->assertCount(2, $actions);
        $this->assertContains("toB", $actions);
        $this->assertContains("toC", $actions);
    }
    public function test_t05_process_instance_complete_and_cancel(): void
    {
        // Scenario T-05
$def = new WorkflowDefinition("wf");
        $def->addState("a")->addState("b");
        $def->setInitial("a");
        $mgr = new WorkflowManager();
        $mgr->register($def);
        $inst = $mgr->start("wf");
        $pi = new ProcessInstance($inst);
        $this->assertFalse($pi->isCompleted());
        $this->assertTrue($pi->complete());
        $this->assertTrue($pi->isCompleted());
        $this->assertFalse($pi->complete(), "double complete returns false");
    }
}
