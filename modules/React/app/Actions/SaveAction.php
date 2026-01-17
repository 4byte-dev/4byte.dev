<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserSavedEvent;
use Modules\React\Services\ReactService;

class SaveAction
{
    public function __construct(
        protected ReactService $reactService
    ) {
    }

    /**
     * Execute the action.
     */
    public function execute(string $saveableType, int $saveableId, int $userId): void
    {
        $this->reactService->cacheSave($saveableType, $saveableId, $userId);

        event(new UserSavedEvent($saveableType, $saveableId, $userId));
    }
}
