<?php

namespace Modules\Category\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Category\Models\CategoryProfile;

class CategoryProfileObserver
{
    /**
     * Handle the "updated" event for the CategoryProfile model.
     */
    public function updated(CategoryProfile $categoryProfile): void
    {
        Cache::forget("category:{$categoryProfile->category->id}:profile");
    }

    /**
     * Handle the "deleted" event for the CategoryProfile model.
     */
    public function deleted(CategoryProfile $categoryProfile): void
    {
        Cache::forget("category:{$categoryProfile->category->id}:profile");
    }
}
