<?php

namespace Modules\Article\Tests\Unit\Listeners;

use Carbon\Carbon;
use Mockery;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Listeners\ArticleSyncGorseListener;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\Category\Models\Category;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;
use Modules\Tag\Models\Tag;

class ArticlePublishedGorseListenerTest extends TestCase
{
    public function test_listener_inserts_article_to_gorse(): void
    {
        $article = Article::factory()->create([
            'published_at' => now(),
            'status'       => ArticleStatus::PUBLISHED,
        ]);
        $category = Category::factory()->create();
        $tag      = Tag::factory()->create();

        $article->categories()->attach($category);
        $article->tags()->attach($tag);

        $event = new ArticlePublishedEvent($article);

        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('insertItem')
            ->once()
            ->with(Mockery::on(function (GorseItem $item) use ($article, $category, $tag) {
                $expectedLabels = collect(["tag:{$tag->id}"])
                    ->merge(["category:{$category->id}"])
                    ->merge(['article', "user:{$article->user_id}"])
                    ->all();

                $expectedItem = new GorseItem(
                    "article:{$article->id}",
                    ['article', "user:{$article->user_id}"],
                    $expectedLabels,
                    $article->slug,
                    false,
                    Carbon::parse($article->published_at)->toDateTimeString()
                );

                $actualJson   = json_encode($item);
                $expectedJson = json_encode($expectedItem);

                return $actualJson === $expectedJson;
            }));

        $listener = new ArticleSyncGorseListener($gorseService);
        $listener->handleArticlePublished($event);
    }

    public function test_listener_deletes_item_from_gorse(): void
    {
        $article = Article::factory()->create();
        $event   = new ArticleDeletedEvent($article);

        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteItem')
            ->once()
            ->with("article:{$article->id}");

        $listener = new ArticleSyncGorseListener($gorseService);
        $listener->handleArticleDeleted($event);
    }
}
