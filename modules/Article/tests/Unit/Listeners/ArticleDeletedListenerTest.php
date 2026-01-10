<?php

namespace Modules\Article\Tests\Unit\Listeners;

use Mockery;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Listeners\ArticleDeletedListener;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\Recommend\Services\GorseService;

class ArticleDeletedListenerTest extends TestCase
{
    public function test_listener_deletes_item_from_gorse(): void
    {
        $article = Article::factory()->create();
        $event   = new ArticleDeletedEvent($article);

        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteItem')
            ->once()
            ->with("article:{$article->id}");

        $listener = new ArticleDeletedListener();
        $listener->handle($event, $gorseService);
    }
}
