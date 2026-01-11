<?php

namespace Modules\Article\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class ArticleDeletedEventTest extends TestCase
{
    public function test_event_has_article(): void
    {
        $article = Article::factory()->make();
        $event   = new ArticleDeletedEvent($article);

        $this->assertSame($article, $event->article);
    }

    public function test_event_dispatch(): void
    {
        Event::fake();

        $article = Article::factory()->create();
        ArticleDeletedEvent::dispatch($article);

        Event::assertDispatched(ArticleDeletedEvent::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }
}
