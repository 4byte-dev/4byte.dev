<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Cache;
use Modules\User\Data\UserData;
use Modules\User\Data\UserProfileData;
use Modules\User\Models\User;
use Modules\User\Models\UserProfile;

class UserService
{
    /**
     * Retrieve user data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $userId): UserData
    {
        $user = Cache::rememberForever("user:{$userId}", function () use ($userId) {
            return User::query()
                ->select(['id', 'name', 'username', 'created_at'])
                ->findOrFail($userId);
        });

        return UserData::fromModel($user);
    }

    /**
     * Retrieve the ID of a user by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $username): int
    {
        return Cache::rememberForever("user:{$username}:id", function () use ($username) {
            return User::query()
                ->select(['id'])
                ->where('username', $username)
                ->firstOrFail()->id;
        });
    }

    /**
     * Retrieve profile information for a given user.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProfileData(int $userId): UserProfileData
    {
        return Cache::rememberForever("user:{$userId}:profile", function () use ($userId) {
            $userProfile = UserProfile::query()
                ->where('user_id', $userId)
                ->select(['id', 'role', 'bio', 'location', 'website', 'socials'])
                ->firstOrFail();

            return UserProfileData::fromModel($userProfile);
        });
    }
}
