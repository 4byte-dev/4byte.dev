<?php

namespace Modules\Tag\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\News\Events\NewsDeletedEvent;
use Modules\News\Models\News;
use Modules\React\Services\ReactService;
use Modules\Tag\Listeners\NewsDeletedListener;
use Modules\Tag\Models\Tag;
use Modules\Tag\Tests\TestCase;

class NewsDeletedListenerTest extends TestCase
{
    public function test_it_decrements_news_count_for_tags(): void
    {
        Event::fake();

        $tag  = Tag::factory()->create();
        $news = News::factory()->create();
        $news->tags()->attach($tag);

        $reactService = Mockery::mock(ReactService::class);
        $reactService->shouldReceive('decrementCount')
            ->once()
            ->with(Tag::class, $tag->id, 'news');

        $listener = new NewsDeletedListener($reactService);
        $event    = new NewsDeletedEvent($news);

        $listener->handle($event);
    }
}
