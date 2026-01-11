<?php

namespace Modules\Category\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Category\Models\Category;
use Modules\News\Events\NewsPublishedEvent;
use Modules\React\Services\ReactService;

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

        $news->categories()->each(function ($category) {
            $this->reactService->incrementCount(Category::class, $category->id, "news");
        });
    }
}
