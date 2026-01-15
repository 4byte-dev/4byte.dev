<?php

namespace Modules\React\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserUncommentedEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $userId,
        public string $commentableType,
        public int $commentableId,
        public ?int $commentId = null
    ) {
    }
}
