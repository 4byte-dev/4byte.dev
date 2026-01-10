<?php

namespace Modules\Article\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Modules\Article\Jobs\RemoveArticleFromGorse;
use Modules\Article\Jobs\SyncArticleToGorse;
use Modules\Article\Models\Article;
use Modules\Article\Observers\ArticleObserver;
use Modules\Article\Tests\TestCase;

class ArticleObserverTest extends TestCase
{
    private ArticleObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new ArticleObserver();
    }

    public function test_saved_dispatches_sync_job_if_published(): void
    {
        Queue::fake();

        $article = Article::factory()->make(['status' => 'PUBLISHED', 'id' => 1]);
        $article->setRelation('tags', collect([]));
        $article->setRelation('categories', collect([]));

        $this->observer->saved($article);

        Queue::assertPushed(SyncArticleToGorse::class, function ($job) use ($article) {
            return $job->article->id === $article->id;
        });
    }

    public function test_saved_does_not_dispatch_if_not_published(): void
    {
        Queue::fake();

        $article = Article::factory()->make(['status' => 'DRAFT']);

        $this->observer->saved($article);

        Queue::assertNotPushed(SyncArticleToGorse::class);
    }

    public function test_updated_clears_cache(): void
    {
        $article = Article::factory()->make(['id' => 1]);

        Cache::shouldReceive('forget')
            ->once()
            ->with("article:1");

        $this->observer->updated($article);
    }

    public function test_deleted_dispatches_remove_job_and_clears_cache(): void
    {
        Queue::fake();

        $article = Article::factory()->make(['id' => 1, 'slug' => 'slug']);

        Cache::shouldReceive('forget')->once()->with("article:slug:id");
        Cache::shouldReceive('forget')->once()->with("article:1");
        Cache::shouldReceive('forget')->once()->with("article:1:likes");
        Cache::shouldReceive('forget')->once()->with("article:1:dislikes");

        $this->observer->deleted($article);

        Queue::assertPushed(RemoveArticleFromGorse::class, function ($job) use ($article) {
            return $job->articleId === $article->id;
        });
    }
}
