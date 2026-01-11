<?php

namespace Modules\React\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\React\Models\Follow;
use Modules\React\Services\ReactService;

trait CanFollow
{
    /**
     * @return HasMany<Follow, $this>
     */
    public function followings(): HasMany
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    /**
     * Get the total number of followings for this model.
     */
    public function followingsCount(): int
    {
        return app(ReactService::class)->getFollowingsCount($this->getKey());
    }

    /**
     * Follow a given target model.
     *
     * @param object $target
     */
    public function follow($target): void
    {
        if (! $this->isFollowing($target)) {
            app(ReactService::class)->insertFollow($target->getMorphClass(), $target->getKey(), $this->getKey());
        }
    }

    /**
     * Unfollow a given target model.
     *
     * @param object $target
     */
    public function unfollow($target): void
    {
        app(ReactService::class)->deleteFollow($target->getMorphClass(), $target->getKey(), $this->getKey());
    }

    /**
     * Determine if this model is following the given target.
     *
     * @param object $target
     */
    public function isFollowing($target): bool
    {
        return app(ReactService::class)->checkFollowed($target->getMorphClass(), $target->getKey(), $this->getKey());
    }
}
