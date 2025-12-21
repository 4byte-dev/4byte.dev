<?php

namespace Modules\Article\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class ArticlePublishedEventTest extends TestCase
{
    public function test_event_has_article(): void
    {
        $article = Article::factory()->make();
        $event   = new ArticlePublishedEvent($article);

        $this->assertSame($article, $event->article);
    }

    public function test_event_dispatch(): void
    {
        Event::fake();

        $article = Article::factory()->create();
        ArticlePublishedEvent::dispatch($article);

        Event::assertDispatched(ArticlePublishedEvent::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }
}
