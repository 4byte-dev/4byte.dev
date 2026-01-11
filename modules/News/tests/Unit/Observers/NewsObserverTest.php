<?php

namespace Modules\News\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Modules\News\Enums\NewsStatus;
use Modules\News\Events\NewsDeletedEvent;
use Modules\News\Events\NewsPublishedEvent;
use Modules\News\Models\News;
use Modules\News\Observers\NewsObserver;
use Modules\News\Tests\TestCase;

class NewsObserverTest extends TestCase
{
    private NewsObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new NewsObserver();
    }

    public function test_saved_inserts_item_to_gorse(): void
    {
        Event::fake();

        $news = News::factory()->create([
            'id'     => 1,
            'status' => NewsStatus::PUBLISHED,
        ]);
        $news->setRelation('tags', collect([]));
        $news->setRelation('categories', collect([]));

        $this->observer->saved($news);

        Event::assertDispatched(NewsPublishedEvent::class, function ($event) use ($news) {
            return $event->news->id === $news->id;
        });
    }

    public function test_updated_updates_gorse_and_clears_cache(): void
    {
        Event::fake();

        $news = News::factory()->create([
            'id'     => 1,
            'status' => NewsStatus::PUBLISHED,
            'slug'   => 'slug',
        ]);
        $news->setRelation('tags', collect([]));
        $news->setRelation('categories', collect([]));

        Cache::shouldReceive('forget')
            ->once()
            ->with('news:1');

        $this->observer->updated($news);

        Event::assertNotDispatched(NewsPublishedEvent::class);
    }

    public function test_updated_clears_old_slug_cache_if_slug_changed(): void
    {
        Event::fake();

        $news = News::factory()->create([
            'id'     => 1,
            'status' => NewsStatus::PUBLISHED,
            'slug'   => 'new-slug',
        ]);

        Cache::shouldReceive('forget')->once()->with('news:1');

        $this->observer->updated($news);

        Event::assertNotDispatched(NewsPublishedEvent::class);
    }

    public function test_deleted_removes_from_gorse_and_clears_cache(): void
    {
        Event::fake();

        $news = News::factory()->create([
            'id'   => 1,
            'slug' => 'slug',
        ]);

        Cache::shouldReceive('forget')->once()->with('news:slug:id');
        Cache::shouldReceive('forget')->once()->with('news:1');

        $this->observer->deleted($news);

        Event::assertDispatched(NewsDeletedEvent::class, function ($event) use ($news) {
            return $event->news->id === $news->id;
        });
    }
}
