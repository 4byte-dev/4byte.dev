<?php

namespace Modules\News\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Modules\News\Events\NewsDeletedEvent;
use Modules\News\Models\News;
use Modules\News\Tests\TestCase;

class NewsDeletedEventTest extends TestCase
{
    public function test_event_has_news(): void
    {
        $news  = News::factory()->make();
        $event = new NewsDeletedEvent($news);

        $this->assertSame($news, $event->news);
    }

    public function test_event_dispatch(): void
    {
        Event::fake();

        $news = News::factory()->create();
        NewsDeletedEvent::dispatch($news);

        Event::assertDispatched(NewsDeletedEvent::class, function ($event) use ($news) {
            return $event->news->id === $news->id;
        });
    }
}
