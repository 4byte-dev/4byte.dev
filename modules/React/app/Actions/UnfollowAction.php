<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserUnfollowedEvent;
use Modules\React\Services\ReactService;

class UnfollowAction
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
        $this->reactService->cacheDeleteFollow($followableType, $followableId, $followerId);

        event(new UserUnfollowedEvent($followerId, $followableType, $followableId));
    }
}
