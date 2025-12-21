<?php

namespace Modules\Category\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Category\Data\CategoryData;
use Modules\Category\Data\CategoryProfileData;
use Modules\Category\Services\CategoryService;
use Modules\Category\Tests\TestCase;
use Modules\Tag\Data\TagData;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends TestCase
{
    public function test_it_displays_category_detail_page(): void
    {
        $categoryId = 1;
        $slug       = 'test-category';

        $categoryData = new CategoryData(
            id: $categoryId,
            name: 'Test Category',
            slug: $slug,
            followers: 0,
            isFollowing: false
        );

        $profileData = new CategoryProfileData(
            id: 10,
            description: 'Test Description',
            color: '#fff'
        );

        $tagData = new TagData(
            id: 5,
            name: 'Test Tag',
            slug: 'test-tag',
            followers: 0,
            isFollowing: false
        );

        $tags = collect([$tagData]);

        $categoryService = Mockery::mock(CategoryService::class);
        $categoryService->shouldReceive('getId')
            ->once()
            ->with($slug)
            ->andReturn($categoryId);

        $categoryService->shouldReceive('getData')
            ->once()
            ->with($categoryId)
            ->andReturn($categoryData);

        $categoryService->shouldReceive('getProfileData')
            ->once()
            ->with($categoryId)
            ->andReturn($profileData);

        $categoryService->shouldReceive('getArticlesCount')
            ->once()
            ->with($categoryId)
            ->andReturn(10);

        $categoryService->shouldReceive('getNewsCount')
            ->once()
            ->with($categoryId)
            ->andReturn(5);

        $categoryService->shouldReceive('listTags')
            ->once()
            ->with($categoryId)
            ->andReturn($tags);

        $this->app->instance(CategoryService::class, $categoryService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getCategorySeo')
            ->once()
            ->with($categoryData, $profileData)
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('category.view', $slug));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
            ->component('Category/Detail')

            ->where('category.id', $categoryId)
            ->where('category.slug', $slug)
            ->where('category.name', 'Test Category')

            ->where('profile.description', 'Test Description')
            ->where('profile.color', '#fff')

            ->where('articles', 10)
            ->where('news', 5)

            ->has('tags', 1)
            ->where('tags.0.id', 5)
            ->where('tags.0.name', 'Test Tag')
            ->where('tags.0.slug', 'test-tag')
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
