<?php

namespace Modules\Article\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class ArticleSyncGorseListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected GorseService $gorseService
    ) {
    }

    /**
     * Handle article published event.
     */
    public function handleArticlePublished(ArticlePublishedEvent $event): void
    {
        $article = $event->article;

        $gorseItem = new GorseItem(
            "article:{$article->id}",
            ['article', "user:{$article->user_id}"],
            $article->tags->pluck('id')
                ->map(fn ($id) => "tag:{$id}")
                ->merge(
                    $article->categories->pluck('id')
                        ->map(fn ($id) => "category:{$id}")
                )
                ->merge(['article', "user:{$article->user_id}"])
                ->all(),
            $article->slug,
            $article->status !== ArticleStatus::PUBLISHED,
            Carbon::parse($article->published_at)->toDateTimeString()
        );

        $this->gorseService->insertItem($gorseItem);
    }

    /**
     * Handle article deleted event.
     */
    public function handleArticleDeleted(ArticleDeletedEvent $event): void
    {
        $this->gorseService->deleteItem("article:{$event->article->id}");
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            ArticlePublishedEvent::class,
            [self::class, 'handleArticlePublished']
        );

        $events->listen(
            ArticleDeletedEvent::class,
            [self::class, 'handleArticleDeleted']
        );
    }
}
