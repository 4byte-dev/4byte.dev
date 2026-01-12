<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserLikedEvent;
use Modules\React\Services\ReactService;

class LikeAction
{
    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Execute the action.
     */
    public function execute(string $likeableType, int $likeableId, int $userId): void
    {
        if ($this->reactService->checkDisliked($likeableType, $likeableId, $userId)) {
            $this->reactService->cacheDeleteDislike($likeableType, $likeableId, $userId);
        }

        $this->reactService->cacheLike($likeableType, $likeableId, $userId);

        event(new UserLikedEvent($userId, $likeableType, $likeableId));
    }
}
