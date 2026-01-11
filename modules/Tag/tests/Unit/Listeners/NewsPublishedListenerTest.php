<?php

namespace Modules\Tag\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\News\Events\NewsPublishedEvent;
use Modules\News\Models\News;
use Modules\React\Services\ReactService;
use Modules\Tag\Listeners\NewsPublishedListener;
use Modules\Tag\Models\Tag;
use Modules\Tag\Tests\TestCase;

class NewsPublishedListenerTest extends TestCase
{
    public function test_it_increments_news_count_for_tags(): void
    {
        Event::fake();

        $tag  = Tag::factory()->create();
        $news = News::factory()->create();
        $news->tags()->attach($tag);

        $reactService = Mockery::mock(ReactService::class);
        $reactService->shouldReceive('incrementCount')
            ->once()
            ->with(Tag::class, $tag->id, 'news');

        $listener = new NewsPublishedListener($reactService);
        $event    = new NewsPublishedEvent($news);

        $listener->handle($event);
    }
}
