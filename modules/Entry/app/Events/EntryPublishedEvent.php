<?php

namespace Modules\Entry\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Entry\Models\Entry;

class EntryPublishedEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Entry $entry)
    {
    }
}
