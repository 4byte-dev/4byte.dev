<?php

namespace Modules\News\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Modules\News\Enums\NewsStatus;
use Modules\News\Models\News;
use Modules\News\Observers\NewsObserver;
use Modules\News\Tests\TestCase;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class NewsObserverTest extends TestCase
{
    private GorseService|MockInterface $gorse;

    private NewsObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gorse    = Mockery::mock(GorseService::class);
        $this->observer = new NewsObserver($this->gorse);
    }

    public function test_saved_inserts_item_to_gorse(): void
    {
        $news = News::factory()->create([
            'id'     => 1,
            'status' => NewsStatus::PUBLISHED,
        ]);
        $news->setRelation('tags', collect([]));
        $news->setRelation('categories', collect([]));

        $this->gorse->shouldReceive('insertItem')
            ->once()
            ->with(Mockery::type(GorseItem::class));

        $this->observer->saved($news);
    }

    public function test_updated_updates_gorse_and_clears_cache(): void
    {
        $news = News::factory()->create([
            'id'     => 1,
            'status' => NewsStatus::PUBLISHED,
            'slug'   => 'slug',
        ]);
        $news->setRelation('tags', collect([]));
        $news->setRelation('categories', collect([]));

        $this->gorse->shouldReceive('updateItem')
            ->once()
            ->with(Mockery::type(GorseItem::class));

        Cache::shouldReceive('forget')
            ->once()
            ->with('news:1');

        $this->observer->updated($news);
    }

    public function test_updated_clears_old_slug_cache_if_slug_changed(): void
    {
        $news = News::factory()->create([
            'id'     => 1,
            'status' => NewsStatus::PUBLISHED,
            'slug'   => 'new-slug',
        ]);

        $this->gorse->shouldReceive('updateItem')
            ->once()
            ->with(Mockery::type(GorseItem::class));

        Cache::shouldReceive('forget')->once()->with('news:1');

        $this->observer->updated($news);
    }

    public function test_deleted_removes_from_gorse_and_clears_cache(): void
    {
        $news = News::factory()->create([
            'id'   => 1,
            'slug' => 'slug',
        ]);

        $this->gorse->shouldReceive('deleteItem')
            ->once()
            ->with('news:1');

        Cache::shouldReceive('forget')->once()->with('news:slug:id');
        Cache::shouldReceive('forget')->once()->with('news:1');

        $this->observer->deleted($news);
    }
}
