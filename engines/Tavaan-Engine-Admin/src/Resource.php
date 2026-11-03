<?php

declare(strict_types=1);

namespace Tavaan\Engine\Admin;

/**
 * Resource — admin CRUD resource definition.
 */
final class Resource
{
    /** @var array<string, mixed> */
    private array $columns = [];
    /** @var list<string> */
    private array $searchable = [];
    /** @var list<string> */
    private array $filterable = [];
    /** @var list<string> */
    private array $sortable = [];
    private ?Form $form = null;
    /** @var list<string> */
    private array $actions = [];
    /** @var list<string> */
    private array $showFields = [];

    public function __construct(public readonly string $name, public readonly string $controller) {}

    public function title(): string
    {
        return ucfirst($this->name);
    }

    /**
     * columns — set the list-view columns.
     * @param array<string, mixed> $columns
     */
    public function columns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    public function getColumns(): array { return $this->columns; }

    /**
     * searchable — mark fields searchable.
     * @param list<string> $fields
     */
    public function searchable(array $fields): self
    {
        $this->searchable = $fields;
        return $this;
    }

    public function getSearchable(): array { return $this->searchable; }

    /**
     * filterable — mark fields filterable.
     * @param list<string> $fields
     */
    public function filterable(array $fields): self
    {
        $this->filterable = $fields;
        return $this;
    }

    public function getFilterable(): array { return $this->filterable; }

    /**
     * sortable — mark fields sortable.
     * @param list<string> $fields
     */
    public function sortable(array $fields): self
    {
        $this->sortable = $fields;
        return $this;
    }

    public function getSortable(): array { return $this->sortable; }

    /**
     * form — register a Form instance for create/edit.
     */
    public function form(?Form $form = null): Form|self
    {
        if ($form === null) {
            return $this->form ?? new Form($this->name);
        }
        $this->form = $form;
        return $this;
    }

    /**
     * actions — define the row-level action buttons.
     * @param list<string> $actions
     */
    public function actions(array $actions): self
    {
        $this->actions = $actions;
        return $this;
    }

    public function getActions(): array { return $this->actions; }

    /**
     * show — set the detail-view field list.
     * @param list<string> $fields
     */
    public function show(array $fields): self
    {
        $this->showFields = $fields;
        return $this;
    }

    public function getShowFields(): array { return $this->showFields; }
}
