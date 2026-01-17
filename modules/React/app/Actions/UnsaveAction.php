<?php

namespace Modules\React\Actions;

use Modules\React\Events\UserUnsavedEvent;
use Modules\React\Services\ReactService;

class UnsaveAction
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
        $this->reactService->cacheDeleteSave($saveableType, $saveableId, $userId);

        event(new UserUnsavedEvent($saveableType, $saveableId, $userId));
    }
}
