<?php

declare(strict_types=1);

namespace Tavaan\Engine\Audit;

/**
 * Auditor — hash-chained audit log.
 */
final class Auditor
{
    /** @var list<array<string, mixed>> */
    private array $entries = [];
    private string $lastHash = '';
    private string $secret;

    public function __construct(string $secret = '')
    {
        $this->secret = $secret !== '' ? $secret : bin2hex(random_bytes(32));
    }

    /**
     * record — append an immutable audit entry to the chain.
     * @param array<string, mixed> $data
     */
    public function record(array $data): string
    {
        $entry = [
            'id' => bin2hex(random_bytes(8)),
            'time' => time(),
            'data' => $data,
            'prev' => $this->lastHash,
        ];
        $entry['hash'] = $this->hashEntry($entry);
        $this->entries[] = $entry;
        $this->lastHash = $entry['hash'];
        return $entry['id'];
    }

    /**
     * verify — validate the entire chain (each hash links to previous).
     */
    public function verify(): bool
    {
        $prev = '';
        foreach ($this->entries as $entry) {
            if ($entry['prev'] !== $prev) { return false; }
            $expected = $this->hashEntry($entry);
            if (!hash_equals($entry['hash'], $expected)) { return false; }
            $prev = $entry['hash'];
        }
        return true;
    }

    /**
     * export — return all entries (optionally filtered).
     * @return list<array<string, mixed>>
     */
    public function export(?callable $filter = null): array
    {
        if ($filter === null) { return $this->entries; }
        return array_values(array_filter($this->entries, $filter));
    }

    /**
     * prune — remove entries older than N days.
     */
    public function prune(int $olderThanDays = 90): int
    {
        $cutoff = time() - ($olderThanDays * 86400);
        $before = count($this->entries);
        $this->entries = array_values(array_filter(
            $this->entries,
            fn($e) => $e['time'] >= $cutoff
        ));
        return $before - count($this->entries);
    }

    /**
     * lastHash — return the most recent chain hash.
     */
    public function lastHash(): string
    {
        return $this->lastHash;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function entries(): array { return $this->entries; }

    public function count(): int { return count($this->entries); }

    private function hashEntry(array $entry): string
    {
        return hash_hmac('sha256', json_encode([
            'id' => $entry['id'],
            'time' => $entry['time'],
            'data' => $entry['data'],
            'prev' => $entry['prev'],
        ]), $this->secret);
    }
}
