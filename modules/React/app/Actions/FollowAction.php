<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserFollowedEvent;
use Modules\React\Services\ReactService;

class FollowAction
{
    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Execute the action.
     */
    public function execute(string $followableType, int $followableId, int $followerId): void
    {
        $this->reactService->cacheFollow($followableType, $followableId, $followerId);

        event(new UserFollowedEvent($followerId, $followableType, $followableId));
    }
}
