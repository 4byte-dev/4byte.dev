<?php

namespace Modules\Recommend\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Modules\Article\Data\ArticleData;
use Modules\Article\Services\ArticleService;
use Modules\Category\Services\CategoryService;
use Modules\Recommend\Services\FeedService;
use Modules\Recommend\Services\GorseService;
use Modules\Recommend\Tests\TestCase;
use Modules\Tag\Services\TagService;
use Modules\User\Data\UserData;

class FeedServiceTest extends TestCase
{
    private FeedService $service;

    private GorseService|MockInterface $gorseService;

    private ArticleService $articleService;

    private TagService $tagService;

    private CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gorseService    = Mockery::mock(GorseService::class);
        $this->articleService  = Mockery::mock(ArticleService::class);
        $this->tagService      = Mockery::mock(TagService::class);
        $this->categoryService = Mockery::mock(CategoryService::class);

        $this->service = new FeedService(
            $this->gorseService,
            $this->articleService,
            $this->tagService,
            $this->categoryService
        );
    }

    public function test_articles_returns_cached_popular_articles(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(fn () => [new ArticleData(
                id: 1,
                title: 'Test Article',
                slug: 'test-article',
                excerpt: 'excerpt',
                content: 'content',
                image: [
                    'image'      => 'https://cdn.4byte.dev/logo.png',
                    'responsive' => [],
                    'srcset'     => '',
                    'thumb'      => null,
                ],
                user: new UserData(1, 'User', 'user', '', 0, 0, false, now()),
                categories: [],
                tags: [],
                sources: [],
                likes: 0,
                dislikes: 0,
                comments: 0,
                isLiked: false,
                isDisliked: false,
                isSaved: false,
                canUpdate: false,
                canDelete: false,
                published_at: null
            )]);

        $articles = $this->service->articles();

        $this->assertCount(1, $articles);
        $this->assertEquals('Test Article', $articles[0]->title);
    }

    public function test_get_personalized_recommendations_usage(): void
    {
        $this->gorseService->shouldReceive('getRecommend')
            ->once()
            ->with('1', 10, 0)
            ->andReturnUsing(fn () => ['item:1']);

        $recommendations = $this->service->getPersonalizedRecommendations(1, [], 10, 0);

        $this->assertEquals(['item:1'], $recommendations);
    }

    public function test_get_personalized_recommendations_with_filter(): void
    {
        $this->gorseService->shouldReceive('getRecommendByCategory')
            ->once()
            ->with('1', 10, 0, ['tech'])
            ->andReturnUsing(fn () => ['item:2']);

        $recommendations = $this->service->getPersonalizedRecommendations(1, ['tech'], 10, 0);

        $this->assertEquals(['item:2'], $recommendations);
    }
}
