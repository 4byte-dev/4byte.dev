<?php

namespace Modules\React\Observers;

use Carbon\Carbon;
use Modules\React\Models\Dislike;
use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Services\GorseService;

class DislikeObserver
{
    protected GorseService $gorse;

    public function __construct(GorseService $gorse)
    {
        $this->gorse = $gorse;
    }

    /**
     * Handle the "created" event for the Dislike model.
     */
    public function created(Dislike $dislike): void
    {
        /** @phpstan-ignore-next-line */
        $feedback = new GorseFeedback('dislike', (string) $dislike->user_id, strtolower(class_basename($dislike->dislikeable)) . ':' . $dislike->dislikeable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    /**
     * Handle the "deleted" event for the Dislike model.
     */
    public function deleted(Dislike $dislike): void
    {
        /* @phpstan-ignore-next-line */
        $this->gorse->deleteFeedback('dislike', (string) $dislike->user_id, strtolower(class_basename($dislike->dislikeable)) . ':' . $dislike->dislikeable->id);
    }
}
