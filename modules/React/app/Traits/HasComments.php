<?php

namespace Modules\React\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\React\Models\Comment;
use Modules\React\Services\ReactService;

trait HasComments
{
    /**
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Determine if the specified user has commented on this model.
     */
    public function isCommentedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return app(ReactService::class)->checkCommented($this->getMorphClass(), $this->getKey(), $userId);
    }

    /**
     * Add a new comment by a specific user.
     */
    public function comment(int $userId, string $content, ?int $parentId = null): void
    {
        app(ReactService::class)->insertComment($this->getMorphClass(), $this->getKey(), $content, $userId, $parentId);
    }

    /**
     * Retrieve the total number of comments for this model.
     */
    public function commentsCount(): int
    {
        return app(ReactService::class)->getCommentsCount($this->getMorphClass(), $this->getKey());
    }

    /**
     * Retrieve the number of replies for a specific parent comment.
     */
    public function commentRepliesCount(int $parentId): int
    {
        return app(ReactService::class)->getCommentRepliesCount($this->getMorphClass(), $this->getKey(), $parentId);
    }
}
