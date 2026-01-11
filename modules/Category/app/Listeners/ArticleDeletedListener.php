<?php

namespace Modules\Category\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Category\Models\Category;
use Modules\React\Services\ReactService;

class ArticleDeletedListener implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(ArticleDeletedEvent $event): void
    {
        $article = $event->article;

        $article->categories()->each(function ($category) {
            $this->reactService->decrementCount(Category::class, $category->id, 'articles');
        });
    }
}
