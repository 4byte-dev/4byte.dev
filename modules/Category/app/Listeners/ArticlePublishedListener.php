<?php

namespace Modules\Category\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Category\Models\Category;
use Modules\React\Services\ReactService;

class ArticlePublishedListener implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ReactService $reactService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(ArticlePublishedEvent $event): void
    {
        $article = $event->article;

        $article->categories()->each(function ($category) {
            $this->reactService->incrementCount(Category::class, $category->id, "articles");
        });
    }
}
