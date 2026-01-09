<?php

namespace Modules\Search\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Search\Services\SearchService;
use Modules\Search\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class SearchControllerTest extends TestCase
{
    public function test_it_displays_search_detail_page(): void
    {
        $query = 'laravel';

        $results = [
            [
                'id'    => 1,
                'type'  => 'article',
                'title' => 'Laravel Test Article',
                'slug'  => 'laravel-test-article',
            ],
            [
                'id'    => 2,
                'type'  => 'category',
                'title' => 'Laravel Category',
                'slug'  => 'laravel-category',
            ],
        ];

        $searchService = Mockery::mock(SearchService::class);
        $searchService->shouldReceive('search')
            ->once()
            ->with($query)
            ->andReturn($results);

        $this->app->instance(SearchService::class, $searchService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')
            ->once()
            ->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getSearchSEO')
            ->once()
            ->with($query)
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('search.view', ['q' => $query]));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Search/Detail')

                ->where('q', $query)

                ->has('results', 2)

                ->where('results.0.id', 1)
                ->where('results.0.type', 'article')
                ->where('results.0.title', 'Laravel Test Article')
                ->where('results.0.slug', 'laravel-test-article')

                ->where('results.1.id', 2)
                ->where('results.1.type', 'category')
                ->where('results.1.title', 'Laravel Category')
                ->where('results.1.slug', 'laravel-category')
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
