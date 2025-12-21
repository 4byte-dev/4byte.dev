<?php

namespace Modules\Tag\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Tag\Models\TagProfile;

class TagProfileObserver
{
    public function updated(TagProfile $tag): void
    {
        Cache::forget("tag:{$tag->id}:profile");
    }

    public function deleted(TagProfile $tag): void
    {
        Cache::forget("tag:{$tag->id}:profile");
    }
}
