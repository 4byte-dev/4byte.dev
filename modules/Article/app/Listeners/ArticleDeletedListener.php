<?php

namespace Modules\Article\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Recommend\Services\GorseService;

class ArticleDeletedListener implements ShouldQueue
{
    use Queueable;

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
     * Handle the event.
     */
    public function handle(ArticleDeletedEvent $event, GorseService $gorse): void
    {
        $gorse->deleteItem("article:{$event->article->id}");
    }
}
