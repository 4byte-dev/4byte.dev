<?php

namespace Modules\Tag\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\React\Services\ReactService;
use Modules\Tag\Models\Tag;

class ArticleDeletedListener implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ReactService $reactService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(ArticleDeletedEvent $event): void
    {
        $article = $event->article;

        $article->tags()->each(function ($tag) {
            $this->reactService->decrementCount(Tag::class, $tag->id, "articles");
        });
    }
}
