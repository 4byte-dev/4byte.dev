<?php

namespace Modules\Tag\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Models\Article;
use Modules\React\Services\ReactService;
use Modules\Tag\Listeners\ArticlePublishedListener;
use Modules\Tag\Models\Tag;
use Modules\Tag\Tests\TestCase;

class ArticlePublishedListenerTest extends TestCase
{
    public function test_it_increments_article_count_for_tags(): void
    {
        Event::fake();

        $tag     = Tag::factory()->create();
        $article = Article::factory()->create();
        $article->tags()->attach($tag);

        $reactService = Mockery::mock(ReactService::class);
        $reactService->shouldReceive('incrementCount')
            ->once()
            ->with(Tag::class, $tag->id, 'articles');

        $listener = new ArticlePublishedListener($reactService);
        $event    = new ArticlePublishedEvent($article);

        $listener->handle($event);
    }
}
