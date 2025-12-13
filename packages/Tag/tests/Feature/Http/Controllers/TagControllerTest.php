<?php

namespace Packages\Tag\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Packages\Tag\Data\TagData;
use Packages\Tag\Data\TagProfileData;
use Packages\Tag\Services\TagService;
use Packages\Tag\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class TagControllerTest extends TestCase
{
    public function test_it_displays_tag_detail_page(): void
    {
        $tagId = 1;
        $slug  = 'test-tag';

        $tagData = new TagData(
            id: $tagId,
            name: 'Test Tag',
            slug: $slug,
            followers: 0,
            isFollowing: false
        );

        $profileData = new TagProfileData(
            id: 10,
            description: 'Test Description',
            color: '#fff',
            categories: []
        );

        $relatedTags = collect([$tagData]);

        $tagService = Mockery::mock(TagService::class);
        $tagService->shouldReceive('getId')
            ->once()
            ->with($slug)
            ->andReturn($tagId);

        $tagService->shouldReceive('getData')
            ->once()
            ->with($tagId)
            ->andReturn($tagData);

        $tagService->shouldReceive('getProfileData')
            ->once()
            ->with($tagId)
            ->andReturn($profileData);

        $tagService->shouldReceive('getArticlesCount')
            ->once()
            ->with($tagId)
            ->andReturn(10);

        $tagService->shouldReceive('getNewsCount')
            ->once()
            ->with($tagId)
            ->andReturn(5);

        $tagService->shouldReceive('listRelated')
            ->once()
            ->with($tagId)
            ->andReturn($relatedTags);

        $this->app->instance(TagService::class, $tagService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getTagSeo')
            ->once()
            ->with($tagData, $profileData)
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('tag.view', $slug));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
            ->component('Tag/Detail')

            ->where('tag.id', $tagId)
            ->where('tag.slug', $slug)
            ->where('tag.name', 'Test Tag')

            ->where('profile.description', 'Test Description')
            ->where('profile.color', '#fff')

            ->where('articles', 10)
            ->where('news', 5)

            ->has('tags', 1)
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
