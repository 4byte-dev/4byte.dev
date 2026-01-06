<?php

namespace Modules\Page\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification as NotificationFacade;
use Modules\Page\Events\PagePublishedEvent;
use Modules\Page\Listeners\PagePublishedListener;
use Modules\Page\Models\Page;
use Modules\Page\Notifications\PagePublishedNotification;
use Modules\Page\Tests\TestCase;

class PagePublishedListenerTest extends TestCase
{
    public function test_listener_sends_notifications(): void
    {
        NotificationFacade::fake();

        $page     = Page::factory()->create();
        $event    = new PagePublishedEvent($page);
        $listener = new PagePublishedListener();

        $listener->handle($event);

        NotificationFacade::assertSentTo(
            $page->user,
            PagePublishedNotification::class,
            function ($notification, $channels) use ($page) {
                return $notification->page->id === $page->id;
            }
        );
    }
}
