<?php

namespace Modules\React\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserDislikedEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $userId,
        public string $dislikeableType,
        public int $dislikeableId
    ) {
    }
}
