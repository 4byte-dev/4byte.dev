<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserUncommentedEvent;
use Modules\React\Models\Comment;
use Modules\React\Services\ReactService;

class DeleteCommentAction
{
    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Execute the action.
     */
    public function execute(Comment $comment): void
    {
        $this->reactService->cacheDeleteComment(
            $comment->commentable_type,
            $comment->commentable_id,
            $comment->user_id,
            $comment->parent_id
        );

        event(new UserUncommentedEvent(
            $comment->user_id,
            $comment->commentable_type,
            $comment->commentable_id,
            $comment->id
        ));
    }
}
