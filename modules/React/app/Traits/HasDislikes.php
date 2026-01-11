<?php

namespace Modules\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\React\Models\Dislike;
use Modules\React\Services\ReactService;

trait HasDislikes
{
    /**
     * @return MorphMany<Dislike, $this>
     */
    public function dislikes(): MorphMany
    {
        return $this->morphMany(Dislike::class, 'dislikeable');
    }

    /**
     * Determine whether the given user has disliked this model.
     */
    public function isDislikedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return app(ReactService::class)->checkDisliked($this->getMorphClass(), $this->getKey(), $userId);
    }

    /**
     * Add a dislike from the given user.
     */
    public function dislike(int $userId): void
    {
        if (! $this->isDislikedBy($userId)) {
            app(ReactService::class)->insertDislike($this->getMorphClass(), $this->getKey(), $userId);
        }
    }

    /**
     * Remove a dislike by the given user.
     */
    public function undislike(int $userId): void
    {
        app(ReactService::class)->deleteDislike($this->getMorphClass(), $this->getKey(), $userId);
    }

    /**
     * Toggle the dislike state for the given user.
     */
    public function toggleDislike(int $userId): void
    {
        $this->isDislikedBy($userId)
            ? $this->undislike($userId)
            : $this->dislike($userId);
    }

    /**
     * Retrieve the total number of dislikes for this model.
     */
    public function dislikesCount(): int
    {
        return app(ReactService::class)->getDislikesCount($this->getMorphClass(), $this->getKey());
    }
}
