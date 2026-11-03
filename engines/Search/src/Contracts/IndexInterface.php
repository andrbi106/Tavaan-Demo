<?php

declare(strict_types=1);

namespace Tavaan\Engine\Search\Contracts;

/**
 * قرارداد Index — ایندکس‌گذاری و جست‌وجو.
 */
interface IndexInterface
{
    public function index(string $docId, string $text, float $boost = 1.0): void;

    public function delete(string $docId): void;

    /**
     * @return list<array{docId: string, score: float}>
     */
    public function search(string $query, int $limit = 20): array;

    public function suggest(string $prefix, int $limit = 5): array;

    public function count(): int;
}
