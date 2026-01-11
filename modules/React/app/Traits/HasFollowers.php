<?php

namespace Modules\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\React\Models\Follow;
use Modules\React\Services\ReactService;

trait HasFollowers
{
    /**
     * @return MorphMany<Follow, $this>
     */
    public function followers(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    /**
     * Retrieve the total number of followers for this model.
     */
    public function followersCount(): int
    {
        return app(ReactService::class)->getFollowersCount($this->getMorphClass(), $this->getKey());
    }

    /**
     * Determine if the specified user is following this model.
     */
    public function isFollowedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return app(ReactService::class)->checkFollowed($this->getMorphClass(), $this->getKey(), $userId);
    }
}
