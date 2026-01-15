<?php

namespace Modules\React\Services;

use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\React\Data\CommentData;
use Modules\React\Mappers\CommentMapper;
use Modules\React\Models\Comment;
use Modules\React\Models\Count;
use Modules\React\Models\Dislike;
use Modules\React\Models\Follow;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

class ReactService
{
    /**
     * @var array<string, string|Model>
     */
    protected static array $classes = [];

    /**
     * @var array<string, callable|string>
     */
    protected static array $callbacks = [];

    /**
     * Register handler.
     *
     * @param string $name
     * @param string|Model $class
     * @param callable|string $callback
     *
     * @return void
     */
    public static function registerHandler(string $name, string|Model $class, callable|string $callback): void
    {
        self::$classes[$name]   = $class;
        self::$callbacks[$name] = $callback;
    }

    public static function getClass(string $name): Model|string|null
    {
        return self::$classes[$name] ?? null;
    }

    public static function getCallback(string $name): callable|string|null
    {
        return self::$callbacks[$name] ?? null;
    }

    public function persistLike(string $likeableType, int $likeableId, int $userId): void
    {
        Like::firstOrCreate([
            'user_id'       => $userId,
            'likeable_id'   => $likeableId,
            'likeable_type' => $likeableType,
        ]);

        $this->incrementCountDb($likeableType, $likeableId, 'likes');
    }

    public function cacheLike(string $likeableType, int $likeableId, int $userId): void
    {
        $this->incrementCountCache($likeableType, $likeableId, 'likes');
        Cache::forever($this->cacheKey($likeableType, $likeableId, $userId, 'liked'), true);
    }

    public function persistUnlike(string $likeableType, int $likeableId, int $userId): bool
    {
        $deleted = Like::where('user_id', $userId)
            ->where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->delete();

        if ($deleted) {
            $this->decrementCountDb($likeableType, $likeableId, 'likes');
        }

        return (bool) $deleted;
    }

    public function cacheUnlike(string $likeableType, int $likeableId, int $userId): void
    {
        $this->decrementCountCache($likeableType, $likeableId, 'likes');
        Cache::forget($this->cacheKey($likeableType, $likeableId, $userId, 'liked'));
    }

    /**
     * Returns the total number of likes for the given model.
     */
    public function getLikesCount(string $likeableType, int $likeableId): int
    {
        return $this->getCount($likeableType, $likeableId, 'likes');
    }

    /**
     * Checks if the given user has liked the specified model.
     */
    public function checkLiked(string $likeableType, int $likeableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($likeableType, $likeableId, $userId, 'liked'), function () use ($likeableType, $likeableId, $userId) {
            return Like::where([
                'user_id'       => $userId,
                'likeable_id'   => $likeableId,
                'likeable_type' => $likeableType,
            ])->exists();
        });
    }

    public function persistDislike(string $dislikeableType, int $dislikeableId, int $userId): void
    {
        Dislike::firstOrCreate([
            'user_id'          => $userId,
            'dislikeable_id'   => $dislikeableId,
            'dislikeable_type' => $dislikeableType,
        ]);

        $this->incrementCountDb($dislikeableType, $dislikeableId, 'dislikes');
    }

    public function cacheDislike(string $dislikeableType, int $dislikeableId, int $userId): void
    {
        $this->incrementCountCache($dislikeableType, $dislikeableId, 'dislikes');
        Cache::forever($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'), true);
    }

    public function persistDeleteDislike(string $dislikeableType, int $dislikeableId, int $userId): bool
    {
        $deleted = Dislike::where('user_id', $userId)
            ->where('dislikeable_id', $dislikeableId)
            ->where('dislikeable_type', $dislikeableType)
            ->delete();

        if ($deleted) {
            $this->decrementCountDb($dislikeableType, $dislikeableId, 'dislikes');
        }

        return (bool) $deleted;
    }

    public function cacheDeleteDislike(string $dislikeableType, int $dislikeableId, int $userId): void
    {
        $this->decrementCountCache($dislikeableType, $dislikeableId, 'dislikes');
        Cache::forget($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'));
    }

    /**
     * Returns the total number of dislikes for the given model.
     */
    public function getDislikesCount(string $dislikeableType, int $dislikeableId): int
    {
        return $this->getCount($dislikeableType, $dislikeableId, 'dislikes');
    }

    /**
     * Checks if the given user has disliked the specified model.
     */
    public function checkDisliked(string $dislikeableType, int $dislikeableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($dislikeableType, $dislikeableId, $userId, 'disliked'), function () use ($dislikeableType, $dislikeableId, $userId) {
            return Dislike::where([
                'user_id'          => $userId,
                'dislikeable_id'   => $dislikeableId,
                'dislikeable_type' => $dislikeableType,
            ])->exists();
        });
    }

    public function persistSave(string $saveableType, int $saveableId, int $userId): void
    {
        Save::firstOrCreate([
            'user_id'       => $userId,
            'saveable_id'   => $saveableId,
            'saveable_type' => $saveableType,
        ]);
    }

    public function cacheSave(string $saveableType, int $saveableId, int $userId): void
    {
        Cache::forever($this->cacheKey($saveableType, $saveableId, $userId, 'saved'), true);
    }

    public function persistDeleteSave(string $saveableType, int $saveableId, int $userId): bool
    {
        $deleted = Save::where('user_id', $userId)
            ->where('saveable_id', $saveableId)
            ->where('saveable_type', $saveableType)
            ->delete();

        return (bool) $deleted;
    }

    public function cacheDeleteSave(string $saveableType, int $saveableId, int $userId): void
    {
        Cache::forget($this->cacheKey($saveableType, $saveableId, $userId, 'saved'));
    }

    /**
     * Checks if the given user has saved the specified model.
     */
    public function checkSaved(string $saveableType, int $saveableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($saveableType, $saveableId, $userId, 'saved'), function () use ($saveableType, $saveableId, $userId) {
            return Save::where([
                'user_id'       => $userId,
                'saveable_id'   => $saveableId,
                'saveable_type' => $saveableType,
            ])->exists();
        });
    }

    /**
     * Inserts a comment for the given user on the specified model.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function persistComment(string $commentableType, int $commentableId, string $content, int $userId, ?int $parentId = null): void
    {
        Comment::create([
            'content'          => $content,
            'user_id'          => $userId,
            'parent_id'        => $parentId,
            'commentable_id'   => $commentableId,
            'commentable_type' => $commentableType,
        ]);

        if ($parentId) {
            $this->incrementCountDb(Comment::class, $parentId, 'replies');
        }

        $this->incrementCountDb($commentableType, $commentableId, 'comments');
    }

    public function cacheComment(string $commentableType, int $commentableId, int $userId, ?int $parentId = null): void
    {
        if ($parentId) {
            $this->incrementCountCache(Comment::class, $parentId, 'replies');
            try {
                $replyPaginationKeys = Redis::keys($this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies', 'pagination') . '*');

                if (count($replyPaginationKeys) > 0) {
                    Redis::del(...$replyPaginationKeys);
                }
            } catch (\Exception $e) {
                logger()->warning('Redis is not avaliable: ' . $e->getMessage());
            }
        }

        $this->incrementCountCache($commentableType, $commentableId, 'comments');
        Cache::forever($this->cacheKey($commentableType, $commentableId, $userId, 'commented'), true);

        try {
            $paginationKeys = Redis::keys($this->cacheKey($commentableType, $commentableId, 'comments', 'pagination') . '*');

            if (count($paginationKeys) > 0) {
                Redis::del(...$paginationKeys);
            }
        } catch (\Exception $e) {
            logger()->warning('Redis is not avaliable: ' . $e->getMessage());
        }
    }

    public function persistDeleteComment(int $commentId): bool
    {
        return (bool) Comment::destroy($commentId);
    }

    public function cacheDeleteComment(string $commentableType, int $commentableId, int $userId, ?int $parentId = null): void
    {
        if ($parentId) {
            $this->decrementCountCache(Comment::class, $parentId, 'replies');
            try {
                $replyPaginationKeys = Redis::keys($this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies', 'pagination') . '*');

                if (count($replyPaginationKeys) > 0) {
                    Redis::del(...$replyPaginationKeys);
                }
            } catch (\Exception $e) {
                logger()->warning('Redis is not avaliable: ' . $e->getMessage());
            }
        }

        $this->decrementCountCache($commentableType, $commentableId, 'comments');
        Cache::forget($this->cacheKey($commentableType, $commentableId, $userId, 'commented'));

        try {
            $paginationKeys = Redis::keys($this->cacheKey($commentableType, $commentableId, 'comments', 'pagination') . '*');

            if (count($paginationKeys) > 0) {
                Redis::del(...$paginationKeys);
            }
        } catch (\Exception $e) {
            logger()->warning('Redis is not avaliable: ' . $e->getMessage());
        }
    }

    /**
     * Checks if the given user has commented on the specified model.
     */
    public function checkCommented(string $commentableType, int $commentableId, int $userId): bool
    {
        return Cache::rememberForever($this->cacheKey($commentableType, $commentableId, $userId, 'commented'), function () use ($commentableType, $commentableId, $userId) {
            return Comment::where([
                'user_id'          => $userId,
                'commentable_id'   => $commentableId,
                'commentable_type' => $commentableType,
            ])->exists();
        });
    }

    /**
     * Returns the total number of comments for the given model.
     */
    public function getCommentsCount(string $commentableType, int $commentableId): int
    {
        return $this->getCount($commentableType, $commentableId, 'comments');
    }

    /**
     * Returns a single comment by its ID.
     */
    public function getComment(int $commentId): CommentData
    {
        $comment = Cache::rememberForever($this->cacheKey(Comment::class, $commentId, 'comment', $commentId), function () use ($commentId) {
            return Comment::where('id', $commentId)->first();
        });

        return CommentMapper::toData($comment, false, false, false, true, true, true);
    }

    /**
     * Returns a paginated collection of top-level comments for the given model.
     *
     * @return array<CommentData>
     */
    public function getComments(string $commentableType, int $commentableId, int $page, int $perPage): array
    {
        $comments = Cache::rememberForever(
            $this->cacheKey($commentableType, $commentableId, 'comments', 'pagination', (string) $page, (string) $perPage),
            fn () => Comment::where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType)
                ->whereNull('parent_id')
                ->orderByDesc('created_at')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
        );

        return CommentMapper::collection($comments, false, false, false, true, true, true);
    }

    /**
     * Returns the number of replies for a specific comment.
     */
    public function getCommentRepliesCount(?int $parentId): int
    {
        if (! $parentId) {
            return 0;
        }

        return $this->getCount(Comment::class, $parentId, 'replies');
    }

    /**
     * Returns a paginated collection of replies for a specific comment.
     *
     * @return array<CommentData>
     */
    public function getCommentReplies(string $commentableType, int $commentableId, int $parentId, int $page, int $perPage): array
    {
        $comments = Cache::rememberForever(
            $this->cacheKey($commentableType, $commentableId, 'comment', $parentId, 'replies', 'pagination', $page, $perPage),
            fn () => Comment::where('commentable_id', $commentableId)
                ->where('commentable_type', $commentableType)
                ->where('parent_id', $parentId)
                ->orderByDesc('created_at')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
        );

        return CommentMapper::collection($comments);
    }

    /**
     * Inserts a follow relationship for the given user.
     */
    public function persistFollow(string $followableType, int $followableId, int $followerId): void
    {
        Follow::firstOrCreate([
            'follower_id'     => $followerId,
            'followable_id'   => $followableId,
            'followable_type' => $followableType,
        ]);

        $this->incrementCountDb($followableType, $followableId, 'followers');
        $this->incrementCountDb(User::class, $followerId, 'followings');
    }

    public function cacheFollow(string $followableType, int $followableId, int $followerId): void
    {
        $this->incrementCountCache($followableType, $followableId, 'followers');
        $this->incrementCountCache(User::class, $followerId, 'followings');
        Cache::forever($this->cacheKey($followableType, $followableId, $followerId, 'followed'), true);
    }

    /**
     * Deletes a follow relationship for the given user.
     */
    public function persistDeleteFollow(string $followableType, int $followableId, int $followerId): bool
    {
        $deleted = Follow::where('follower_id', $followerId)
            ->where('followable_id', $followableId)
            ->where('followable_type', $followableType)
            ->delete();

        if ($deleted) {
            $this->decrementCountDb($followableType, $followableId, 'followers');
            $this->decrementCountDb(User::class, $followerId, 'followings');
        }

        return (bool) $deleted;
    }

    public function cacheDeleteFollow(string $followableType, int $followableId, int $followerId): void
    {
        $this->decrementCountCache($followableType, $followableId, 'followers');
        $this->decrementCountCache(User::class, $followerId, 'followings');
        Cache::forget($this->cacheKey($followableType, $followableId, $followerId, 'followed'));
    }

    /**
     * Returns the number of followers for a specific model.
     */
    public function getFollowersCount(string $followableType, int $followableId): int
    {
        return $this->getCount($followableType, $followableId, 'followers');
    }

    /**
     * Returns the number of users the given user is following.
     */
    public function getFollowingsCount(int $userId): int
    {
        return $this->getCount(User::class, $userId, 'followings');
    }

    /**
     * Checks if the given user is following the specified model.
     */
    public function checkFollowed(string $followableType, int $followableId, int $followerId): bool
    {
        return Cache::rememberForever($this->cacheKey($followableType, $followableId, $followerId, 'followed'), function () use ($followerId, $followableId, $followableType) {
            return Follow::where([
                'follower_id'     => $followerId,
                'followable_id'   => $followableId,
                'followable_type' => $followableType,
            ])->exists();
        });
    }

    /**
     * Increment the count for a specific filter (DB + Cache).
     */
    public function incrementCount(string $countableType, int $countableId, string $filter, int $amount = 1): void
    {
        $this->incrementCountDb($countableType, $countableId, $filter, $amount);

        if (Cache::has("react:counts:{$this->cacheKey($countableType, $countableId, $filter)}")) {
            $this->incrementCountCache($countableType, $countableId, $filter, $amount);
        } else {
            $this->getCount($countableType, $countableId, $filter);
        }
    }

    public function incrementCountDb(string $countableType, int $countableId, string $filter, int $amount = 1): void
    {
        Count::firstOrCreate([
            'countable_type' => $countableType,
            'countable_id'   => $countableId,
            'filter'         => $filter,
        ], ['count' => 0]);

        Count::where('countable_type', $countableType)
            ->where('countable_id', $countableId)
            ->where('filter', $filter)
            ->increment('count', $amount);
    }

    public function incrementCountCache(string $countableType, int $countableId, string $filter, int $amount = 1): void
    {
        $cacheKey = "react:counts:{$this->cacheKey($countableType, $countableId, $filter)}";

        if (! Cache::has($cacheKey)) {
            Cache::rememberForever($cacheKey, function () use ($countableType, $countableId, $filter) {
                return Count::where([
                    'countable_type' => $countableType,
                    'countable_id'   => $countableId,
                    'filter'         => $filter,
                ])->value('count') ?? 0;
            });
        }

        Cache::increment($cacheKey, $amount);
    }

    /**
     * Decrement the count for a specific filter (DB + Cache).
     */
    public function decrementCount(string $countableType, int $countableId, string $filter, int $amount = 1): void
    {
        $this->decrementCountDb($countableType, $countableId, $filter, $amount);

        if (Cache::has("react:counts:{$this->cacheKey($countableType, $countableId, $filter)}")) {
            $this->decrementCountCache($countableType, $countableId, $filter, $amount);
        } else {
            $this->getCount($countableType, $countableId, $filter);
        }
    }

    public function decrementCountDb(string $countableType, int $countableId, string $filter, int $amount = 1): void
    {
        Count::firstOrCreate([
            'countable_type' => $countableType,
            'countable_id'   => $countableId,
            'filter'         => $filter,
        ], ['count' => 0]);

        Count::where('countable_type', $countableType)
            ->where('countable_id', $countableId)
            ->where('filter', $filter)
            ->update(['count' => DB::raw("GREATEST(count - {$amount}, 0)")]);
    }

    public function decrementCountCache(string $countableType, int $countableId, string $filter, int $amount = 1): void
    {
        $cacheKey = "react:counts:{$this->cacheKey($countableType, $countableId, $filter)}";

        if (! Cache::has($cacheKey)) {
            Cache::rememberForever($cacheKey, function () use ($countableType, $countableId, $filter) {
                return Count::where([
                    'countable_type' => $countableType,
                    'countable_id'   => $countableId,
                    'filter'         => $filter,
                ])->value('count') ?? 0;
            });
        }

        Cache::decrement($cacheKey, $amount);
    }

    /**
     * Get the count for a specific filter.
     */
    public function getCount(string $countableType, int $countableId, string $filter): int
    {
        $cacheKey = "react:counts:{$this->cacheKey($countableType, $countableId, $filter)}";

        return Cache::rememberForever($cacheKey, function () use ($countableType, $countableId, $filter) {
            return Count::where([
                'countable_type' => $countableType,
                'countable_id'   => $countableId,
                'filter'         => $filter,
            ])->value('count') ?? 0;
        });
    }

    /**
     * Generates a cache key for reactable entities.
     *
     * @param string|int ...$parts
     */
    protected function cacheKey(string $reactableType, int $reactableId, ...$parts): string
    {
        $base = strtolower(class_basename($reactableType));

        return "{$base}:{$reactableId}:" . implode(':', $parts);
    }
}
