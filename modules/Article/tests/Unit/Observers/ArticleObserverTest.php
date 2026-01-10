<?php

namespace Modules\Article\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Events\ArticlePublishedEvent;
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

    public function test_saved_dispatches_publish_event_if_published(): void
    {
        Event::fake();

        $article = Article::factory()->make(['status' => 'PUBLISHED', 'id' => 1]);
        $article->setRelation('tags', collect([]));
        $article->setRelation('categories', collect([]));

        $this->observer->saved($article);

        Event::assertDispatched(ArticlePublishedEvent::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }

    public function test_saved_does_not_dispatch_if_not_published(): void
    {
        Event::fake();

        $article = Article::factory()->make(['status' => 'DRAFT']);

        $this->observer->saved($article);

        Event::assertNotDispatched(ArticlePublishedEvent::class);
    }

    public function test_updated_clears_cache(): void
    {
        $article = Article::factory()->make(['id' => 1]);

        Cache::shouldReceive('forget')
            ->once()
            ->with("article:1");

        $this->observer->updated($article);
    }

    public function test_deleted_dispatches_delete_event_and_clears_cache(): void
    {
        Event::fake();

        $article = Article::factory()->make(['id' => 1, 'slug' => 'slug']);

        Cache::shouldReceive('forget')->once()->with("article:slug:id");
        Cache::shouldReceive('forget')->once()->with("article:1");
        Cache::shouldReceive('forget')->once()->with("article:1:likes");
        Cache::shouldReceive('forget')->once()->with("article:1:dislikes");

        $this->observer->deleted($article);

        Event::assertDispatched(ArticleDeletedEvent::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }
}
