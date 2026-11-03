<?php

declare(strict_types=1);

namespace Tavaan\Engine\Search\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Search\SearchManager;
use Tavaan\Engine\Search\SearchServiceProvider;
use Tavaan\Engine\Search\Contracts\IndexInterface;

/**
 * تست‌های Feature برای موتور Search.
 */
final class SearchFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new SearchServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(SearchManager::class, $provider->getManager());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new SearchServiceProvider([]);
        $this->assertContains(SearchManager::class, $provider->provides());
        $this->assertContains(IndexInterface::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/search.php';
        $this->assertArrayHasKey('driver', $config);
        $this->assertArrayHasKey('per_page', $config);
        $this->assertArrayHasKey('highlight', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/search.php';
        $this->assertArrayHasKey('error_index_not_found', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/search.php';
        $this->assertArrayHasKey('error_index_not_found', $messages);
    }

    public function test_end_to_end_indexing_and_search(): void
    {
        $provider = new SearchServiceProvider([]);
        $provider->register();
        $m = $provider->getManager();
        $m->addDocument('products', 'p1', 'لپ تاپ ایسوس ROG');
        $m->addDocument('products', 'p2', 'لپ تاپ لنوو ThinkPad');
        $m->addDocument('products', 'p3', 'گوشی سامسونگ');

        $results = $m->search('products', 'لپ تاپ');
        $this->assertCount(2, $results);

        // Delete one and verify
        $m->deleteDocument('products', 'p1');
        $results = $m->search('products', 'لپ تاپ');
        $this->assertCount(1, $results);
        $this->assertSame('p2', $results[0]['docId']);
    }
}
