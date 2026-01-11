<?php

namespace Modules\Tag\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\React\Services\ReactService;
use Modules\Tag\Models\Tag;

class ArticlePublishedListener implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(ArticlePublishedEvent $event): void
    {
        $article = $event->article;

        $article->tags()->each(function ($tag) {
            $this->reactService->incrementCount(Tag::class, $tag->id, 'articles');
        });
    }
}
