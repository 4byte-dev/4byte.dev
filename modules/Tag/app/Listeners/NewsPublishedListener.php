<?php

namespace Modules\Tag\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\News\Events\NewsPublishedEvent;
use Modules\React\Services\ReactService;
use Modules\Tag\Models\Tag;

class NewsPublishedListener implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ReactService $reactService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(NewsPublishedEvent $event): void
    {
        $news = $event->news;

        $news->tags()->each(function ($tag) {
            $this->reactService->incrementCount(Tag::class, $tag->id, "news");
        });
    }
}
