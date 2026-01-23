<?php

namespace Modules\Entry\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Entry\Events\EntryDeletedEvent;
use Modules\Entry\Models\Entry;

class CreateEntryAction
{
    public function execute(Entry $entry): Entry
    {
        return DB::transaction(function () use ($entry) {
            $entry->delete();

            Cache::forget("entry:{$entry->slug}:id");
            Cache::forget("entry:{$entry->id}");

            event(new EntryDeletedEvent($entry));

            return $entry;
        });
    }
}
