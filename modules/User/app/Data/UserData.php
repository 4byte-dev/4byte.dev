<?php

namespace Modules\User\Data;

use DateTime;

readonly class UserData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $username,
        public readonly ?string $avatar,
        public readonly int $followers,
        public readonly int $followings,
        public readonly bool $isFollowing,
        public readonly DateTime $created_at,
        public readonly string $type = 'user'
    ) {
    }
}
