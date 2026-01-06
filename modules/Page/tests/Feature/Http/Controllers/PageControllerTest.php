<?php

namespace Modules\Page\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Page\Data\PageData;
use Modules\Page\Services\PageService;
use Modules\Page\Tests\TestCase;
use Modules\User\Data\UserData;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends TestCase
{
    public function test_it_displays_page_detail_page(): void
    {
        $pageId = 1;
        $slug   = 'test-page';

        $userData = new UserData(
            id: 10,
            name: 'Test User',
            username: 'testuser',
            avatar: '',
            followers: 10,
            followings: 3,
            isFollowing: true,
            created_at: now()
        );

        $pageData = new PageData(
            id: $pageId,
            title: 'Test Page',
            slug: $slug,
            content: 'Test page content',
            excerpt: 'Page excerpt',
            image: [
                'image'      => 'https://cdn.4byte.dev/logo.png',
                'responsive' => [],
                'srcset'     => '',
                'thumb'      => null,
            ],
            user: $userData,
            canUpdate: false,
            canDelete: false,
            published_at: now(),
            type: 'page'
        );

        $pageService = Mockery::mock(PageService::class);
        $pageService->shouldReceive('getId')
            ->once()
            ->with($slug)
            ->andReturn($pageId);

        $pageService->shouldReceive('getData')
            ->once()
            ->with($pageId)
            ->andReturn($pageData);

        $this->app->instance(PageService::class, $pageService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')
            ->once()
            ->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getPageSEO')
            ->once()
            ->with($pageData, $userData)
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('page.view', $slug));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Page/Detail')

                ->has('page')

                ->where('page.id', $pageId)
                ->where('page.title', 'Test Page')
                ->where('page.slug', $slug)
                ->where('page.content', 'Test page content')
                ->where('page.excerpt', 'Page excerpt')

                ->where('page.image.image', 'https://cdn.4byte.dev/logo.png')

                ->where('page.user.id', 10)
                ->where('page.user.name', 'Test User')
                ->where('page.user.username', 'testuser')
                ->where('page.user.followers', 10)
                ->where('page.user.followings', 3)
                ->where('page.user.isFollowing', true)

                ->where('page.canUpdate', false)
                ->where('page.canDelete', false)
                ->where('page.type', 'page')
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
