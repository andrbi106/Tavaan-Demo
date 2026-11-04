<?php

declare(strict_types=1);

namespace Tavaan\Engine\Report;

/**
 * ReportExporter — render a Report to CSV/XLSX/HTML/JSON/PDF and stream it.
 */
final class ReportExporter
{
    public function __construct(private readonly Report $report) {}

    /**
     * csv — render to CSV string.
     */
    public function csv(): string
    {
        $rows = $this->report->data;
        if ($rows === []) { return ''; }
        $columns = $this->report->meta['columns'] ?? array_keys($rows[0]);
        $out = implode(',', $this->csvRow($columns)) . "\n";
        foreach ($rows as $row) {
            $values = [];
            foreach ($columns as $c) {
                $values[] = $row[$c] ?? '';
            }
            $out .= implode(',', $this->csvRow($values)) . "\n";
        }
        return $out;
    }

    /**
     * xlsx — render a minimal XLSX (just CSV with .xlsx mime). Real XLSX requires PhpSpreadsheet.
     */
    public function xlsx(): string
    {
        // Lightweight fallback: emit CSV-like content; for true XLSX use ZipArchive+XML.
        $csv = $this->csv();
        return $csv;
    }

    /**
     * html — render an HTML table.
     */
    public function html(): string
    {
        $rows = $this->report->data;
        $columns = $this->report->meta['columns'] ?? ($rows !== [] ? array_keys($rows[0]) : []);
        $out = '<table class="report"><thead><tr>';
        foreach ($columns as $c) { $out .= '<th>' . htmlspecialchars((string) $c) . '</th>'; }
        $out .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $out .= '<tr>';
            foreach ($columns as $c) {
                $out .= '<td>' . htmlspecialchars((string) ($row[$c] ?? '')) . '</td>';
            }
            $out .= '</tr>';
        }
        $out .= '</tbody></table>';
        return $out;
    }

    public function json(?string $key = null, mixed $default = null): string
    {
        $payload = json_encode($this->report, JSON_UNESCAPED_UNICODE);
        return $payload;
    }

    /**
     * pdf — placeholder PDF (would require TCPDF/FPDF). Returns HTML for now.
     */
    public function pdf(): string
    {
        return '<html><body>' . $this->html() . '</body></html>';
    }

    /**
     * stream — write the rendered content to stdout (or a resource).
     * @param resource|string|null $output
     */
    public function stream(string $format = 'csv', mixed $output = null): int
    {
        $content = match ($format) {
            'csv' => $this->csv(),
            'html' => $this->html(),
            'pdf' => $this->pdf(),
            'json' => $this->json(),
            'xlsx' => $this->xlsx(),
            default => $this->csv(),
        };
        if (is_resource($output)) {
            return fwrite($output, $content);
        }
        echo $content;
        return strlen($content);
    }

    /** @param list<mixed> $values */
    private function csvRow(array $values): array
    {
        return array_map(fn($v) => '"' . str_replace('"', '""', (string) $v) . '"', $values);
    }
}
