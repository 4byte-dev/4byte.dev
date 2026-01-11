<?php

namespace Modules\Tag\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Models\Article;
use Modules\React\Services\ReactService;
use Modules\Tag\Listeners\ArticleDeletedListener;
use Modules\Tag\Models\Tag;
use Modules\Tag\Tests\TestCase;

class ArticleDeletedListenerTest extends TestCase
{
    public function test_it_decrements_article_count_for_tags(): void
    {
        Event::fake();

        $tag     = Tag::factory()->create();
        $article = Article::factory()->create();
        $article->tags()->attach($tag);

        $reactService = Mockery::mock(ReactService::class);
        $reactService->shouldReceive('decrementCount')
            ->once()
            ->with(Tag::class, $tag->id, 'articles');

        $listener = new ArticleDeletedListener($reactService);
        $event    = new ArticleDeletedEvent($article);

        $listener->handle($event);
    }
}
