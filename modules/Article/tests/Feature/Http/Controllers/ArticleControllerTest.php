<?php

namespace Modules\Article\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Article\Data\ArticleData;
use Modules\Article\Services\ArticleService;
use Modules\Article\Tests\TestCase;
use Modules\Category\Data\CategoryData;
use Modules\Tag\Data\TagData;
use Modules\User\Data\UserData;
use Symfony\Component\HttpFoundation\Response;

class ArticleControllerTest extends TestCase
{
    public function test_it_displays_article_detail_page(): void
    {
        $articleId = 1;
        $slug      = 'test-article';

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

        $articleData = new ArticleData(
            id: $articleId,
            title: 'Test Article',
            slug: $slug,
            excerpt: 'Text excerpt',
            content: 'Test content',
            image: [
                'image'      => 'https://cdn.4byte.dev/logo.png',
                'responsive' => [],
                'srcset'     => '',
                'thumb'      => null,
            ],
            user: $userData,
            categories: [
                new CategoryData(
                    id: 1,
                    name: 'Category Test',
                    slug: 'category-test',
                    followers: 10,
                    isFollowing: true,
                ),
            ],
            tags: [
                new TagData(
                    id: 2,
                    name: 'Tag Test',
                    slug: 'tag-test',
                    followers: 10,
                    isFollowing: true,
                ),
            ],
            sources: [
                ['url' => 'https://4byte.dev', 'date' => now()->toString()],
            ],
            likes: 5,
            dislikes: 7,
            comments: 1,
            isLiked: false,
            isDisliked: true,
            isSaved: false,
            canUpdate: false,
            canDelete: false,
            published_at: now()
        );

        $articleService = Mockery::mock(ArticleService::class);
        $articleService->shouldReceive('getId')
            ->once()
            ->with($slug)
            ->andReturn($articleId);

        $articleService->shouldReceive('getData')
            ->once()
            ->with($articleId)
            ->andReturn($articleData);

        $this->app->instance(ArticleService::class, $articleService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getArticleSeo')
            ->once()
            ->with($articleData, $userData)
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('article.view', $slug));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Article/Detail')

                ->has('article')

                ->where('article.id', $articleId)
                ->where('article.title', 'Test Article')
                ->where('article.slug', $slug)
                ->where('article.excerpt', 'Text excerpt')
                ->where('article.content', 'Test content')

                ->where('article.image.image', 'https://cdn.4byte.dev/logo.png')

                ->where('article.user.id', 10)
                ->where('article.user.name', 'Test User')
                ->where('article.user.username', 'testuser')
                ->where('article.user.followers', 10)
                ->where('article.user.followings', 3)
                ->where('article.user.isFollowing', true)

                ->has('article.categories', 1)
                ->where('article.categories.0.name', 'Category Test')
                ->where('article.categories.0.slug', 'category-test')

                ->has('article.tags', 1)
                ->where('article.tags.0.name', 'Tag Test')
                ->where('article.tags.0.slug', 'tag-test')

                ->has('article.sources', 1)
                ->where('article.sources.0.url', 'https://4byte.dev')
                ->where('article.sources.0.url', fn ($url) => $this->isValidUrl($url))
                ->where('article.sources.0.date', fn ($date) => $this->isValidDate($date))

                ->where('article.likes', 5)
                ->where('article.dislikes', 7)
                ->where('article.comments', 1)

                ->where('article.isLiked', false)
                ->where('article.isDisliked', true)
                ->where('article.isSaved', false)
                ->where('article.canUpdate', false)
                ->where('article.canDelete', false)
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
