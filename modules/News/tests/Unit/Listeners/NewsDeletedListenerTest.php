<?php

namespace Modules\News\Tests\Unit\Listeners;

use Mockery;
use Modules\News\Events\NewsDeletedEvent;
use Modules\News\Listeners\NewsDeletedListener;
use Modules\News\Models\News;
use Modules\News\Tests\TestCase;
use Modules\Recommend\Services\GorseService;

class NewsDeletedListenerTest extends TestCase
{
    public function test_listener_deletes_item_from_gorse(): void
    {
        $news  = News::factory()->create();
        $event = new NewsDeletedEvent($news);

        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteItem')
            ->once()
            ->with("news:{$news->id}");

        $listener = new NewsDeletedListener();
        $listener->handle($event, $gorseService);
    }
}
