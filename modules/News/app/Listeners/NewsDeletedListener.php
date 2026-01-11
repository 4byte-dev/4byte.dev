<?php

namespace Modules\News\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\News\Events\NewsDeletedEvent;
use Modules\Recommend\Services\GorseService;

class NewsDeletedListener implements ShouldQueue
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
    public function handle(NewsDeletedEvent $event, GorseService $gorse): void
    {
        $gorse->deleteItem("news:{$event->news->id}");
    }
}
