<?php

namespace Modules\Article\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class ArticlePublishedGorseListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ArticlePublishedEvent $event, GorseService $gorse): void
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
            false,
            Carbon::parse($article->published_at)->toDateTimeString()
        );

        $gorse->insertItem($gorseItem);
    }
}
