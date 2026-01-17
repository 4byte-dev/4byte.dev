<?php

namespace Modules\React\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserUnfollowedEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $followerId,
        public string $followableType,
        public int $followableId
    ) {
    }
}
