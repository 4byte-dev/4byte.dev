<?php

namespace Modules\Page\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Modules\Page\Events\PagePublishedEvent;
use Modules\Page\Models\Page;
use Modules\Page\Tests\TestCase;

class PagePublishedEventTest extends TestCase
{
    public function test_event_has_page(): void
    {
        $page  = Page::factory()->make();
        $event = new PagePublishedEvent($page);

        $this->assertSame($page, $event->page);
    }

    public function test_event_dispatch(): void
    {
        Event::fake();

        $page = Page::factory()->create();
        PagePublishedEvent::dispatch($page);

        Event::assertDispatched(PagePublishedEvent::class, function ($event) use ($page) {
            return $event->page->id === $page->id;
        });
    }
}
