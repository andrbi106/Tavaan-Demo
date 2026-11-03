<?php

declare(strict_types=1);

namespace Tavaan\Engine\Search\Drivers;

use Tavaan\Engine\Search\Contracts\IndexInterface;

/**
 * NativeIndex — ایندکس معکوس خودنوشته با BM25 scoring.
 *
 * مناسب فارسی و استفاده بدون DB.
 *
 * @see docs/03-engines/search.md §4
 */
final class NativeIndex implements IndexInterface
{
    /**
     * @var array<string, array<string, int>> term → [docId → freq]
     */
    private array $index = [];

    /**
     * @var array<string, string> docId → text (برای highlight)
     */
    private array $documents = [];

    /**
     * @var array<string, int> docId → length
     */
    private array $docLengths = [];

    private float $avgDocLength = 0;

    public function index(string $docId, string $text, float $boost = 1.0): void
    {
        // Remove old entry
        $this->delete($docId);

        $tokens = $this->tokenize($text);
        $this->documents[$docId] = $text;
        $this->docLengths[$docId] = \count($tokens);

        foreach ($tokens as $token) {
            $this->index[$token][$docId] = ($this->index[$token][$docId] ?? 0) + (int) $boost;
        }

        $this->updateAvg();
    }

    public function delete(string $docId): void
    {
        if (!isset($this->documents[$docId])) {
            return;
        }
        foreach ($this->index as $term => $docs) {
            if (isset($docs[$docId])) {
                unset($this->index[$term][$docId]);
                if (empty($this->index[$term])) {
                    unset($this->index[$term]);
                }
            }
        }
        unset($this->documents[$docId], $this->docLengths[$docId]);
        $this->updateAvg();
    }

    public function search(string $query, int $limit = 20): array
    {
        $tokens = $this->tokenize($query);
        if (empty($tokens) || empty($this->index)) {
            return [];
        }
        $scores = [];
        $totalDocs = \count($this->documents);
        $k1 = 1.2;  // BM25 k1
        $b = 0.75;  // BM25 b

        foreach ($tokens as $token) {
            if (!isset($this->index[$token])) {
                continue;
            }
            $df = \count($this->index[$token]);
            $idf = \log(($totalDocs + 1) / ($df + 0.5));
            foreach ($this->index[$token] as $docId => $freq) {
                $docLen = $this->docLengths[$docId] ?? 0;
                $norm = 1 - $b + $b * ($docLen / \max(1, $this->avgDocLength));
                $score = $idf * (($freq * ($k1 + 1)) / ($freq + $k1 * $norm));
                $scores[$docId] = ($scores[$docId] ?? 0) + $score;
            }
        }
        \arsort($scores);
        $result = [];
        foreach (\array_slice($scores, 0, $limit, true) as $docId => $score) {
            $result[] = ['docId' => $docId, 'score' => (float) $score];
        }
        return $result;
    }

    public function suggest(string $prefix, int $limit = 5): array
    {
        $prefix = \strtolower($prefix);
        $matches = [];
        foreach (\array_keys($this->index) as $term) {
            if (\str_starts_with($term, $prefix)) {
                $matches[$term] = \count($this->index[$term]);
            }
        }
        \arsort($matches);
        return \array_slice(\array_keys($matches), 0, $limit);
    }

    public function count(): int
    {
        return \count($this->documents);
    }

    /**
     * Get document text by ID.
     */
    public function getDocument(string $docId): ?string
    {
        return $this->documents[$docId] ?? null;
    }

    /**
     * @return list<string>
     */
    public function tokenize(string $text): array
    {
        // Normalize Persian/Arabic characters
        $text = \strtr($text, ['ي' => 'ی', 'ك' => 'ك', 'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا']);
        // Lowercase
        $text = \strtolower($text);
        // Remove punctuation and split
        $text = \preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text) ?? '';
        $tokens = \preg_split('/\s+/u', $text, -1, \PREG_SPLIT_NO_EMPTY) ?: [];
        return \array_values(\array_filter($tokens, fn($t) => \mb_strlen($t) > 1));
    }

    private function updateAvg(): void
    {
        if (empty($this->docLengths)) {
            $this->avgDocLength = 0;
            return;
        }
        $this->avgDocLength = \array_sum($this->docLengths) / \count($this->docLengths);
    }
}
