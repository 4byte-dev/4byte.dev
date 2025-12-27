<?php

namespace Modules\React\Observers;

use Carbon\Carbon;
use Modules\React\Models\Comment;
use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Services\GorseService;

class CommentObserver
{
    protected GorseService $gorse;

    public function __construct(GorseService $gorse)
    {
        $this->gorse = $gorse;
    }

    /**
     * Handle the "created" event for the Comment model.
     */
    public function created(Comment $comment): void
    {
        /** @phpstan-ignore-next-line */
        $feedback = new GorseFeedback('comment', (string) $comment->user_id, strtolower(class_basename($comment->commentable)) . ':' . $comment->commentable->id, '', Carbon::now());
        $this->gorse->insertFeedback($feedback);
    }

    /**
     * Handle the "deleted" event for the Comment model.
     */
    public function deleted(Comment $comment): void
    {
        /* @phpstan-ignore-next-line */
        $this->gorse->deleteFeedback('comment', (string) $comment->user_id, strtolower(class_basename($comment->commentable)) . ':' . $comment->commentable->id);
    }
}
