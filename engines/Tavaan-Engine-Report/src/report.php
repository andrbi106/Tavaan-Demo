<?php
declare(strict_types=1);
namespace Tavaan\Engine\Report;

final class Report
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly array $data = [],
        public readonly array $meta = [],
    ) {}
}

final class ReportBuilder
{
    private array $data = [];
    private array $meta = [];
    private array $columns = [];

    public function __construct(private readonly string $name = 'report') {}

    public function setTitle(string $title): self { $this->meta['title'] = $title; return $this; }
    public function setColumns(array $columns): self { $this->columns = $columns; return $this; }
    public function addRow(array $row): self { $this->data[] = $row; return $this; }
    public function addRows(array $rows): self { foreach ($rows as $row) { $this->data[] = $row; } return $this; }
    public function setMeta(string $key, mixed $value): self { $this->meta[$key] = $value; return $this; }

    public function build(): Report
    {
        return new Report($this->name, $this->meta['title'] ?? ucfirst($this->name), $this->data, array_merge($this->meta, ['columns' => $this->columns, 'row_count' => count($this->data)]));
    }

    public function toCsv(): string
    {
        if (empty($this->data)) return '';
        $csv = '';
        $headers = $this->columns ?: array_keys($this->data[0]);
        $csv .= implode(',', $headers) . "
";
        foreach ($this->data as $row) {
            $values = [];
            foreach ($headers as $h) { $values[] = '"' . str_replace('"', '""', (string)($row[$h] ?? '')) . '"'; }
            $csv .= implode(',', $values) . "
";
        }
        return $csv;
    }

    public function toJson(): string { return json_encode($this->build(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); }
    public function toArray(): array { return $this->build()->data; }
    public function count(): int { return count($this->data); }
}

final class ReportManager
{
    private array $reports = [];

    public function __construct(private readonly array $config = []) {}

    public function register(string $name, callable $builder): self { $this->reports[$name] = $builder; return $this; }
    public function has(string $name): bool { return isset($this->reports[$name]); }
    public function generate(string $name, array $params = []): ?Report { if (!isset($this->reports[$name])) return null; return ($this->reports[$name])($params); }
    public function all(): array { return array_keys($this->reports); }
    public function count(): int { return count($this->reports); }
}

final class ReportServiceProvider
{
    private ?ReportManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new ReportManager($this->config); }
    public function boot(): void {}
    public function provides(): array { return [ReportManager::class, ReportBuilder::class, Report::class]; }
    public function getManager(): ReportManager { if ($this->manager === null) $this->register(); return $this->manager; }

    public function addField(string $name, mixed $value): self
    {
        $this->getManager()->register($name, fn() => $value);
        return $this;
    }
    public function addFilter(string $column, mixed $value): self
    {
        $this->getManager()->register('filter_' . $column, fn() => $value);
        return $this;
    }
    public function dataset(string $name, iterable $rows): self
    {
        $this->getManager()->register($name, function () use ($rows) {
            $builder = new ReportBuilder($name);
            foreach ($rows as $row) {
                $builder->addRow($row);
            }
            return $builder->build();
        });
        return $this;
    }
    private array $reportState = [];

    public function groupBy(string $column): self
    {
        $this->reportState['groupBy'] = $column;
        return $this;
    }

    public function orderBy(string $column, string $direction = "ASC"): self
    {
        $this->reportState['orderBy'] = ['column' => $column, 'direction' => $direction];
        return $this;
    }
    public function pivot(string $name, array $rows, string $rowKey, string $colKey, string $valueKey): Report
    {
        $pivot = [];
        foreach ($rows as $row) {
            $rk = $row[$rowKey] ?? '';
            $ck = $row[$colKey] ?? '';
            $v = $row[$valueKey] ?? 0;
            $pivot[$rk][$ck] = $v;
        }
        $data = [];
        foreach ($pivot as $rk => $cols) {
            $row = [$rowKey => $rk];
            foreach ($cols as $ck => $v) {
                $row[$ck] = $v;
            }
            $data[] = $row;
        }
        return new Report($name, ucfirst($name) . ' Pivot', $data);
    }
    public function run(string $name, array $params = []): ?Report
    {
        return $this->getManager()->generate($name, $params);
    }
}
