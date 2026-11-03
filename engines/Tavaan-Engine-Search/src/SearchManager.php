<?php

declare(strict_types=1);

namespace Tavaan\Engine\Search;

use Tavaan\Engine\Search\Contracts\IndexInterface;
use Tavaan\Engine\Search\Drivers\NativeIndex;

/**
 * SearchManager — مدیریت چندین ایندکس.
 */
final class SearchManager
{
    /**
     * @var array<string, IndexInterface>
     */
    private array $indexes = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function index(string $name): IndexInterface
    {
        if (!isset($this->indexes[$name])) {
            $driver = $this->config['driver'] ?? 'native';
            $this->indexes[$name] = match ($driver) {
                default => new NativeIndex(),
            };
        }
        return $this->indexes[$name];
    }

    public function addDocument(string $indexName, string $docId, string $text, float $boost = 1.0): void
    {
        $this->index($indexName)->index($docId, $text, $boost);
    }

    public function deleteDocument(string $indexName, string $docId): void
    {
        $this->index($indexName)->delete($docId);
    }

    /**
     * @return list<array{docId: string, score: float}>
     */
    public function search(string $indexName, string $query, int $limit = 20): array
    {
        return $this->index($indexName)->search($query, $limit);
    }

    public function suggest(string $indexName, string $prefix, int $limit = 5): array
    {
        return $this->index($indexName)->suggest($prefix, $limit);
    }

    public function count(string $indexName): int
    {
        return $this->index($indexName)->count();
    }
}
