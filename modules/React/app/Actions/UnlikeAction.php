<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Services\ReactService;

class UnlikeAction
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
        $this->reactService->cacheUnlike($likeableType, $likeableId, $userId);

        event(new UserUnlikedEvent($userId, $likeableType, $likeableId));
    }
}
