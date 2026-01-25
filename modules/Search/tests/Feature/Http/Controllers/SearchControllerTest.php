<?php

namespace Modules\Search\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Search\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class SearchControllerTest extends TestCase
{
    public function test_it_displays_search_detail_page(): void
    {
        $query = 'laravel';

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
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
