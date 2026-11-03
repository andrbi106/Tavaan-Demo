<?php

declare(strict_types=1);

namespace Tavaan\Engine\Search\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Search\SearchManager;
use Tavaan\Engine\Search\Drivers\NativeIndex;
use Tavaan\Engine\Search\Contracts\IndexInterface;

/**
 * تست‌های Unit برای موتور Search.
 *
 * @see docs/03-engines/search.md (DOC-052)
 */
final class SearchTest extends TestCase
{
    public function test_native_index_add_and_search(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'لپ تاپ ایسوس');
        $idx->index('doc2', 'لپ تاپ لنوو');
        $results = $idx->search('لپ تاپ');
        $this->assertCount(2, $results);
        $this->assertSame('doc1', $results[0]['docId']);
        $this->assertSame('doc2', $results[1]['docId']);
    }

    public function test_native_index_returns_empty_when_no_match(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'hello world');
        $results = $idx->search('nonexistent');
        $this->assertSame([], $results);
    }

    public function test_native_index_returns_empty_when_no_docs(): void
    {
        $idx = new NativeIndex();
        $this->assertSame([], $idx->search('anything'));
    }

    public function test_native_index_delete(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'hello world');
        $this->assertSame(1, $idx->count());
        $idx->delete('doc1');
        $this->assertSame(0, $idx->count());
        $this->assertSame([], $idx->search('hello'));
    }

    public function test_native_index_delete_nonexistent_is_safe(): void
    {
        $idx = new NativeIndex();
        $idx->delete('nonexistent');
        $this->assertSame(0, $idx->count());
    }

    public function test_native_index_replaces_existing(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'old text');
        $idx->index('doc1', 'new text');
        $this->assertSame(1, $idx->count());
        $results = $idx->search('old');
        $this->assertSame([], $results);
        $results = $idx->search('new');
        $this->assertCount(1, $results);
    }

    public function test_native_index_count(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'a');
        $idx->index('doc2', 'b');
        $idx->index('doc3', 'c');
        $this->assertSame(3, $idx->count());
    }

    public function test_native_index_persian_normalization(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'كتاب');  // Arabic كاف
        $results = $idx->search('كتاب');  // also Arabic
        $this->assertGreaterThan(0, \count($results));
    }

    public function test_native_index_suggest(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'computer');
        $idx->index('doc2', 'computing');
        $idx->index('doc3', 'compare');
        $suggestions = $idx->suggest('comp', 5);
        $this->assertContains('computer', $suggestions);
        $this->assertContains('computing', $suggestions);
        $this->assertContains('compare', $suggestions);
    }

    public function test_native_index_suggest_limit(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'apple');
        $idx->index('doc2', 'application');
        $idx->index('doc3', 'apply');
        $suggestions = $idx->suggest('app', 2);
        $this->assertLessThanOrEqual(2, \count($suggestions));
    }

    public function test_native_index_get_document(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'hello world');
        $this->assertSame('hello world', $idx->getDocument('doc1'));
        $this->assertNull($idx->getDocument('nonexistent'));
    }

    public function test_native_index_implements_interface(): void
    {
        $idx = new NativeIndex();
        $this->assertInstanceOf(IndexInterface::class, $idx);
    }

    public function test_native_index_tokenize(): void
    {
        $idx = new NativeIndex();
        $tokens = $idx->tokenize('Hello, World! This is a test.');
        $this->assertContains('hello', $tokens);
        $this->assertContains('world', $tokens);
        $this->assertContains('test', $tokens);
    }

    public function test_native_index_with_boost(): void
    {
        $idx = new NativeIndex();
        $idx->index('doc1', 'laptop', boost: 1.0);
        $idx->index('doc2', 'laptop laptop', boost: 1.0);
        // doc2 has 'laptop' twice, should rank higher
        $results = $idx->search('laptop');
        $this->assertSame('doc2', $results[0]['docId']);
    }

    public function test_native_index_limit(): void
    {
        $idx = new NativeIndex();
        for ($i = 1; $i <= 10; $i++) {
            $idx->index("doc{$i}", "laptop model {$i}");
        }
        $results = $idx->search('laptop', limit: 3);
        $this->assertCount(3, $results);
    }

    // === SearchManager ===

    public function test_manager_get_named_index(): void
    {
        $m = new SearchManager(['driver' => 'native']);
        $idx = $m->index('products');
        $this->assertInstanceOf(IndexInterface::class, $idx);
    }

    public function test_manager_same_index_returns_same_instance(): void
    {
        $m = new SearchManager([]);
        $i1 = $m->index('products');
        $i2 = $m->index('products');
        $this->assertSame($i1, $i2);
    }

    public function test_manager_add_and_search(): void
    {
        $m = new SearchManager([]);
        $m->addDocument('test', 'd1', 'hello world');
        $m->addDocument('test', 'd2', 'world peace');
        $results = $m->search('test', 'world');
        $this->assertCount(2, $results);
    }

    public function test_manager_delete_document(): void
    {
        $m = new SearchManager([]);
        $m->addDocument('test', 'd1', 'hello');
        $m->deleteDocument('test', 'd1');
        $this->assertSame(0, $m->count('test'));
    }

    public function test_manager_count(): void
    {
        $m = new SearchManager([]);
        $m->addDocument('test', 'd1', 'a');
        $m->addDocument('test', 'd2', 'b');
        $this->assertSame(2, $m->count('test'));
    }

    public function test_manager_suggest(): void
    {
        $m = new SearchManager([]);
        $m->addDocument('test', 'd1', 'computer');
        $m->addDocument('test', 'd2', 'compilation');
        $suggestions = $m->suggest('test', 'comp');
        $this->assertContains('computer', $suggestions);
    }

    public function test_manager_separate_indexes(): void
    {
        $m = new SearchManager([]);
        $m->addDocument('idx1', 'd1', 'shared term');
        $m->addDocument('idx2', 'd2', 'shared term');
        $this->assertSame(1, $m->count('idx1'));
        $this->assertSame(1, $m->count('idx2'));
    }
}
