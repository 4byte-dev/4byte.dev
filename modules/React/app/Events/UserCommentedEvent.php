<?php

namespace Modules\React\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserCommentedEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $userId,
        public string $commentableType,
        public int $commentableId,
        public string $content,
        public ?int $parentId = null
    ) {
    }
}
