<?php

namespace Modules\User\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\User\Models\UserProfile;

class UserProfileObserver
{
    /**
     * Handle the "updated" event for the UserProfile model.
     */
    public function updated(UserProfile $userProfile): void
    {
        Cache::forget("user:{$userProfile->user_id}:profile");
    }

    /**
     * Handle the "deleted" event for the UserProfile model.
     */
    public function deleted(UserProfile $userProfile): void
    {
        Cache::forget("user:{$userProfile->user_id}:profile");
    }
}
