<?php

namespace Packages\Article\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Packages\Article\Models\Article;
use Packages\Article\Observers\ArticleObserver;
use Packages\Article\Tests\TestCase;
use Packages\Recommend\Classes\GorseItem;
use Packages\Recommend\Services\GorseService;

class ArticleObserverTest extends TestCase
{
    private GorseService|MockInterface $gorse;

    private ArticleObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gorse    = Mockery::mock(GorseService::class);
        $this->observer = new ArticleObserver($this->gorse);
    }

    public function test_saved_inserts_item_to_gorse_if_published(): void
    {
        $article = Article::factory()->make(['status' => 'PUBLISHED', 'id' => 1]);
        $article->setRelation('tags', collect([]));
        $article->setRelation('categories', collect([]));

        $this->gorse->shouldReceive('insertItem')
            ->once()
            ->with(Mockery::type(GorseItem::class));

        $this->observer->saved($article);
    }

    public function test_saved_does_not_insert_if_not_published(): void
    {
        $article = Article::factory()->make(['status' => 'DRAFT']);

        $this->gorse->shouldNotReceive('insertItem');

        $this->observer->saved($article);
    }

    public function test_updated_clears_cache(): void
    {
        $article = Article::factory()->make(['id' => 1]);

        Cache::shouldReceive('forget')
            ->once()
            ->with("article:1");

        $this->observer->updated($article);
    }

    public function test_deleted_removes_from_gorse_and_clears_cache(): void
    {
        $article = Article::factory()->make(['id' => 1, 'slug' => 'slug']);

        $this->gorse->shouldReceive('deleteItem')
            ->once()
            ->with("article:1");

        Cache::shouldReceive('forget')->with("article:slug:id");
        Cache::shouldReceive('forget')->with("article:1");
        Cache::shouldReceive('forget')->with("article:1:likes");
        Cache::shouldReceive('forget')->with("article:1:dislikes");

        $this->observer->deleted($article);
    }
}
