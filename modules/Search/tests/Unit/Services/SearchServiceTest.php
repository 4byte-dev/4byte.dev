<?php

namespace Modules\Search\Tests\Unit\Services;

use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Mockery;
use Modules\Search\Services\SearchService;
use Modules\Search\Tests\TestCase;

class SearchServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SearchService::clearHandlers();
    }

    public function test_it_registers_a_search_handler_and_updates_config(): void
    {
        SearchService::registerHandler(
            index: 'articles',
            callback: fn (array $hit) => ['id' => $hit['id']],
            searchableAttributes: ['title', 'content'],
            filterableAttributes: ['user_id'],
            sortableAttributes: ['created_at']
        );

        $handlers = SearchService::getHandlers();

        $this->assertArrayHasKey('articles', $handlers);

        $config = config('scout.meilisearch.index-settings');

        $this->assertArrayHasKey('articles', $config);
        $this->assertEquals(['title', 'content'], $config['articles']['searchableAttributes']);
        $this->assertEquals(['user_id'], $config['articles']['filterableAttributes']);
        $this->assertEquals(['created_at'], $config['articles']['sortableAttributes']);
    }

    public function test_it_searches_across_registered_handlers_and_maps_results(): void
    {
        SearchService::registerHandler(
            'articles',
            fn (array $hit) => [
                'type'  => 'article',
                'id'    => $hit['id'],
                'title' => $hit['title'],
            ],
            [],
            [],
            []
        );

        SearchService::registerHandler(
            'categories',
            fn (array $hit) => [
                'type' => 'category',
                'id'   => $hit['id'],
                'name' => $hit['name'],
            ],
            [],
            [],
            []
        );

        $client = Mockery::mock(Client::class);

        $client->shouldReceive('multiSearch')
            ->once()
            ->with(Mockery::on(function ($queries) {
                $this->assertCount(2, $queries);

                foreach ($queries as $query) {
                    $this->assertInstanceOf(SearchQuery::class, $query);
                }

                return true;
            }))
            ->andReturn([
                'results' => [
                    [
                        'indexUid' => 'articles',
                        'hits'     => [
                            ['id' => 1, 'title' => 'Test Article'],
                        ],
                    ],
                    [
                        'indexUid' => 'categories',
                        'hits'     => [
                            ['id' => 2, 'name' => 'Test Category'],
                        ],
                    ],
                ],
            ]);

        $service = new SearchService($client);

        $results = $service->search('test');

        $this->assertCount(2, $results);

        $this->assertEquals([
            'type'  => 'article',
            'id'    => 1,
            'title' => 'Test Article',
        ], $results[0]);

        $this->assertEquals([
            'type' => 'category',
            'id'   => 2,
            'name' => 'Test Category',
        ], $results[1]);
    }

    public function test_it_returns_raw_hits_when_no_handler_is_registered(): void
    {
        $client = Mockery::mock(Client::class);

        $client->shouldReceive('multiSearch')
            ->once()
            ->andReturn([
                'results' => [
                    [
                        'indexUid' => 'unknown_index',
                        'hits'     => [
                            ['foo' => 'bar'],
                        ],
                    ],
                ],
            ]);

        $service = new SearchService($client);

        $results = $service->search('test');

        $this->assertCount(1, $results);
        $this->assertEquals([['foo' => 'bar']], $results[0]);
    }
}
