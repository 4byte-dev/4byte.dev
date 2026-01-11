<?php

namespace Modules\News\Listeners;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\News\Enums\NewsStatus;
use Modules\News\Events\NewsPublishedEvent;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class NewsPublishedListener implements ShouldQueue
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
    public function handle(NewsPublishedEvent $event, GorseService $gorse): void
    {
        $news = $event->news;

        $gorseItem = new GorseItem(
            "news:{$news->id}",
            ['news', "user:{$news->user_id}"],
            $news->tags->pluck('id')
                ->map(fn ($id) => "tag:{$id}")
                ->merge(
                    $news->categories->pluck('id')
                        ->map(fn ($id) => "category:{$id}")
                )
                ->merge(['news', "user:{$news->user_id}"])
                ->all(),
            $news->slug,
            $news->status != NewsStatus::PUBLISHED,
            Carbon::parse($news->published_at)->toDateTimeString()
        );

        $gorse->insertItem($gorseItem);
    }
}
