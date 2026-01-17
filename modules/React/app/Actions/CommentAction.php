<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserCommentedEvent;
use Modules\React\Services\ReactService;

class CommentAction
{
    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Execute the action.
     */
    public function execute(string $commentableType, int $commentableId, string $content, int $userId, ?int $parentId = null): void
    {
        $this->reactService->cacheComment($commentableType, $commentableId, $userId, $parentId);

        event(new UserCommentedEvent($userId, $commentableType, $commentableId, $content, $parentId));
    }
}
