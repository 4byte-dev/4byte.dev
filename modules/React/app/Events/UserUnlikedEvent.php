<?php

namespace Modules\React\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserUnlikedEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $userId,
        public string $likeableType,
        public int $likeableId
    ) {
    }
}
