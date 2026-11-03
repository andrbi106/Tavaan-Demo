<?php

declare(strict_types=1);

namespace Tavaan\Engine\Report\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Report\ReportBuilder;
use Tavaan\Engine\Report\ReportExporter;

/**
 * T-NN scenario tests for the report engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class ReportScenarioTest extends TestCase
{
    public function test_t01_builder_add_row_and_count(): void
    {
        // Scenario T-01
$b = new ReportBuilder("sales");
        $b->addRow(["id" => 1, "amount" => 100]);
        $b->addRow(["id" => 2, "amount" => 200]);
        $this->assertSame(2, $b->count());
    }
    public function test_t02_builder_to_csv(): void
    {
        // Scenario T-02
$b = new ReportBuilder("sales");
        $b->setColumns(["id", "amount"]);
        $b->addRow(["id" => 1, "amount" => 100]);
        $csv = $b->toCsv();
        $this->assertStringContainsString("id", $csv);
        $this->assertStringContainsString("100", $csv);
    }
    public function test_t03_builder_to_json(): void
    {
        // Scenario T-03
$b = new ReportBuilder("sales");
        $b->addRow(["id" => 1]);
        $json = $b->toJson();
        $this->assertStringContainsString("sales", $json);
    }
    public function test_t04_exporter_csv_output(): void
    {
        // Scenario T-04
$b = new ReportBuilder("sales");
        $b->setColumns(["id"]);
        $b->addRow(["id" => 1]);
        $report = $b->build();
        $ex = new ReportExporter($report);
        $csv = $ex->csv();
        $this->assertStringContainsString("id", $csv);
    }
    public function test_t05_exporter_html_output(): void
    {
        // Scenario T-05
$b = new ReportBuilder("sales");
        $b->setColumns(["id"]);
        $b->addRow(["id" => 1]);
        $ex = new ReportExporter($b->build());
        $html = $ex->html();
        $this->assertStringContainsString("<table", $html);
        $this->assertStringContainsString("</table>", $html);
    }
}
