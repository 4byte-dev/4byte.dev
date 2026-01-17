<?php

namespace Modules\React\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserSavedEvent
{
    use Dispatchable;

    public function __construct(
        public string $saveableType,
        public int $saveableId,
        public int $userId
    ) {
    }
}
