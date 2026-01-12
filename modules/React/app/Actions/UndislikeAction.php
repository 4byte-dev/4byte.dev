<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserUndislikedEvent;
use Modules\React\Services\ReactService;

class UndislikeAction
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
        $this->reactService->cacheDeleteDislike($dislikeableType, $dislikeableId, $userId);

        event(new UserUndislikedEvent($userId, $dislikeableType, $dislikeableId));
    }
}
