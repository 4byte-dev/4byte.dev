<?php

namespace Modules\Article\Tests\Unit\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Modules\Article\Actions\DeleteArticleAction;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class DeleteArticleActionTest extends TestCase
{
    private DeleteArticleAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteArticleAction();
        Event::fake();
    }

    public function test_it_deletes_article_and_clears_cache_and_dispatches_event(): void
    {
        $article = Article::factory()->create();
        Cache::put("article:{$article->slug}:id", $article->id);
        Cache::put("article:{$article->id}", $article);

        $this->action->execute($article);

        $this->assertDatabaseMissing($article->getTable(), ['id' => $article->id]);
        $this->assertFalse(Cache::has("article:{$article->slug}:id"));
        $this->assertFalse(Cache::has("article:{$article->id}"));

        Event::assertDispatched(ArticleDeletedEvent::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }
}
