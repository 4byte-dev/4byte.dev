<?php

namespace Modules\Entry\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Modules\Entry\Events\EntryDeletedEvent;
use Modules\Entry\Events\EntryPublishedEvent;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class EntrySyncGorseListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected GorseService $gorseService
    ) {
    }

    /**
     * Handle entry published event.
     */
    public function handleEntryPublished(EntryPublishedEvent $event): void
    {
        $gorseItem = new GorseItem(
            'entry:' . $event->entry->id,
            ['entry', "user:{$event->entry->user_id}"],
            [],
            '',
            false,
            Carbon::now()->toDateTimeString()
        );
        $this->gorseService->insertItem($gorseItem);
    }

    /**
     * Handle entry deleted event.
     */
    public function handleEntryDeleted(EntryDeletedEvent $event): void
    {
        $this->gorseService->deleteItem("entry:{$event->entry->id}");
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            EntryPublishedEvent::class,
            [self::class, 'handleEntryPublished']
        );

        $events->listen(
            EntryDeletedEvent::class,
            [self::class, 'handleEntryDeleted']
        );
    }
}
