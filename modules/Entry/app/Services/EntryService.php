<?php

namespace Modules\Entry\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Entry\Data\EntryData;
use Modules\Entry\Models\Entry;
use Modules\User\Services\UserService;

class EntryService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Retrieve entry data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $entryId): EntryData
    {
        $entry = Cache::rememberForever("entry:{$entryId}", function () use ($entryId) {
            return Entry::select(['id', 'slug', 'content', 'user_id', 'created_at'])
                ->findOrFail($entryId);
        });

        $user = $this->userService->getData($entry->user_id);

        return EntryData::fromModel($entry, $user);
    }

    /**
     * Retrieve the ID of a entry by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("entry:{$slug}:id", function () use ($slug) {
            return Entry::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }
}
