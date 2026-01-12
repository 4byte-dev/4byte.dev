<?php

namespace Modules\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\React\Actions\LikeAction;
use Modules\React\Actions\UnlikeAction;
use Modules\React\Models\Like;
use Modules\React\Services\ReactService;

trait HasLikes
{
    /**
     * @return MorphMany<Like, $this>
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Determine whether the given user has liked this model.
     */
    public function isLikedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return app(ReactService::class)->checkLiked($this->getMorphClass(), $this->getKey(), $userId);
    }

    /**
     * Add a like from the given user.
     */
    public function like(int $userId): void
    {
        if (! $this->isLikedBy($userId)) {
            app(LikeAction::class)->execute($this->getMorphClass(), $this->getKey(), $userId);
        }
    }

    /**
     * Remove a like by the given user.
     */
    public function unlike(int $userId): void
    {
        app(UnlikeAction::class)->execute($this->getMorphClass(), $this->getKey(), $userId);
    }

    /**
     * Toggle the like state for the given user.
     */
    public function toggleLike(int $userId): void
    {
        $this->isLikedBy($userId)
            ? $this->unlike($userId)
            : $this->like($userId);
    }

    /**
     * Retrieve the total number of likes for this model.
     */
    public function likesCount(): int
    {
        return app(ReactService::class)->getLikesCount($this->getMorphClass(), $this->getKey());
    }
}
