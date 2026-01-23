<?php

namespace Modules\Entry\Mappers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Entry\Data\EntryData;
use Modules\Entry\Models\Entry;
use Modules\User\Data\UserData;

class EntryMapper
{
    /**
     * Create a EntryData instance from a Entry model.
     */
    public static function toData(Entry $entry, UserData $user, bool $setId = false, bool $setPublished = true): EntryData
    {
        $userId = Auth::id();

        return new EntryData(
            id: $setId ? $entry->id : 0,
            slug: $entry->slug,
            content: $entry->content,
            media: $entry->getContentImages(),
            user: $user,
            likes: $entry->likesCount(),
            dislikes: $entry->dislikesCount(),
            comments: $entry->commentsCount(),
            isLiked: $entry->isLikedBy($userId),
            isDisliked: $entry->isDislikedBy($userId),
            isSaved: $entry->isSavedBy($userId),
            canUpdate: Gate::allows('update', $entry),
            canDelete: Gate::allows('delete', $entry),
            published_at: $setPublished ? $entry->created_at : null,
        );
    }
}
