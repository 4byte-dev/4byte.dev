<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserDislikedEvent;
use Modules\React\Services\ReactService;

class DislikeAction
{
    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Execute the action.
     */
    public function execute(string $dislikeableType, int $dislikeableId, int $userId): void
    {
        if ($this->reactService->checkLiked($dislikeableType, $dislikeableId, $userId)) {
            $this->reactService->cacheUnlike($dislikeableType, $dislikeableId, $userId);
        }

        $this->reactService->cacheDislike($dislikeableType, $dislikeableId, $userId);

        event(new UserDislikedEvent($userId, $dislikeableType, $dislikeableId));
    }
}
