<?php

namespace Modules\Entry\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Entry\Events\EntryPublishedEvent;
use Modules\Entry\Models\Entry;

class CreateEntryAction
{
    /**
     * @param array{
     *     content?: string,
     *     media?: array<int, UploadedFile>
     * } $data
     */
    public function execute(array $data): Entry
    {
        return DB::transaction(function () use ($data) {
            $entry = Entry::create([
                'slug'    => Str::uuid()->toString(),
                'content' => $data['content'],
                'user_id' => Auth::id(),
            ]);

            if (isset($data['media'])) {
                $entry->addMultipleMediaFromRequest(['media'])
                    ->each(function ($fileAdder) {
                        $fileAdder->toMediaCollection('content');
                    });
            }

            event(new EntryPublishedEvent($entry));

            return $entry;
        });
    }
}
