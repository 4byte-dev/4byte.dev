<?php

namespace Modules\Tag\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\News\Events\NewsDeletedEvent;
use Modules\React\Services\ReactService;
use Modules\Tag\Models\Tag;

class NewsDeletedListener implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(NewsDeletedEvent $event): void
    {
        $news = $event->news;

        $news->tags()->each(function ($tag) {
            $this->reactService->decrementCount(Tag::class, $tag->id, 'news');
        });
    }
}
