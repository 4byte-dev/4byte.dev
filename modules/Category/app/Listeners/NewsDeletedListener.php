<?php

namespace Modules\Category\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Category\Models\Category;
use Modules\News\Events\NewsDeletedEvent;
use Modules\React\Services\ReactService;

class NewsDeletedListener implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ReactService $reactService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(NewsDeletedEvent $event): void
    {
        $news = $event->news;

        $news->categories()->each(function ($category) {
            $this->reactService->decrementCount(Category::class, $category->id, "news");
        });
    }
}
