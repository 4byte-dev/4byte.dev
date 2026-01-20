<?php

namespace Modules\User\Mappers;

use Illuminate\Support\Facades\Auth;
use Modules\User\Data\UserData;
use Modules\User\Data\UserProfileData;
use Modules\User\Models\User;
use Modules\User\Models\UserProfile;

class UserMapper
{
    /**
     * Create a UserData instance from a User model.
     */
    public static function toData(User $user, bool $setId = false): UserData
    {
        return new UserData(
            id: $setId ? $user->id : 0,
            name: $user->name,
            username: $user->username,
            avatar: $user->getAvatarImage(),
            followers: $user->followersCount(),
            followings: $user->followingsCount(),
            isFollowing: $user->isFollowedBy(Auth::id()),
            created_at: $user->created_at
        );
    }

    /**
     * Create a UserProfileData instance from a UserProfile model.
     */
    public static function toProfileData(UserProfile $userProfile, bool $setId = false): UserProfileData
    {
        return new UserProfileData(
            id: $setId ? $userProfile->id : 0,
            role: $userProfile->role,
            bio: $userProfile->bio,
            location: $userProfile->location,
            website: $userProfile->website,
            socials: $userProfile->socials,
            cover: $userProfile->getCoverImage()
        );
    }

    /**
     * @param iterable<User> $users
     * @param bool $setId
     *
     * @return array<UserData>
     */
    public static function collection(iterable $users, bool $setId = false): array
    {
        $data = [];
        foreach ($users as $user) {
            $data[] = self::toData($user, $setId);
        }

        return $data;
    }
}
