<?php

namespace Modules\Recommend\Tests\Feature\Http\Controllers;

use Mockery;
use Modules\Article\Data\ArticleData;
use Modules\Recommend\Services\FeedService;
use Modules\Recommend\Tests\TestCase;
use Modules\User\Data\UserData;
use Symfony\Component\HttpFoundation\Response;

class FeedControllerTest extends TestCase
{
    public function test_data_returns_top_content(): void
    {
        $feedService = Mockery::mock(FeedService::class);
        $feedService->shouldReceive('categories')->once()->andReturn([]);
        $feedService->shouldReceive('tags')->once()->andReturn([]);
        $feedService->shouldReceive('articles')->once()->andReturn([]);

        $this->app->instance(FeedService::class, $feedService);

        $response = $this->get(route('api.feed.data'));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'categories',
                'tags',
                'articles',
            ]);
    }

    public function test_feed_returns_personalized_recommendations_for_user(): void
    {
        $user = \Modules\User\Models\User::factory()->create();
        $this->actingAs($user);

        $feedService = Mockery::mock(FeedService::class);
        $feedService->shouldReceive('buildFilters')->andReturn([]);
        $feedService->shouldReceive('getPersonalizedRecommendations')
            ->once()
            ->andReturnUsing(fn () => ['article:1']);

        $articleData = new ArticleData(
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
        );

        $feedService->shouldReceive('resolveContents')
            ->with(['article:1'])
            ->once()
            ->andReturnUsing(fn () => [$articleData]);

        $this->app->instance(FeedService::class, $feedService);

        $response = $this->getJson(route('api.feed.feed'));

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_feed_returns_non_personalized_recommendations_for_guest(): void
    {
        $feedService = Mockery::mock(FeedService::class);
        $feedService->shouldReceive('buildFilters')->andReturn([]);
        $feedService->shouldReceive('getNonPersonalizedRecommendations')
            ->once()
            ->andReturnUsing(fn () => ['article:1']);

        $articleData = new ArticleData(
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
        );

        $feedService->shouldReceive('resolveContents')
             ->with(['article:1'])
             ->once()
             ->andReturnUsing(fn () => [$articleData]);

        $this->app->instance(FeedService::class, $feedService);

        $response = $this->getJson(route('api.feed.feed'));

        $response->assertStatus(Response::HTTP_OK);
    }
}
