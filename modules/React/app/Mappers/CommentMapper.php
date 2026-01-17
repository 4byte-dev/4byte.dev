<?php

namespace Modules\React\Mappers;

use Illuminate\Support\Facades\Auth;
use Modules\React\Data\CommentData;
use Modules\React\Models\Comment;
use Modules\User\Services\UserService;

class CommentMapper
{
    public static function toData(
        Comment $comment,
        bool $setId = true,
        bool $setParent = true,
        bool $setReplies = true,
        bool $setLikes = true,
        bool $setLiked = true,
        bool $setContent = false
    ): CommentData {
        $userService = app(UserService::class);

        return new CommentData(
            id: $setId ? $comment->id : 0,
            content: $comment->content,
            parent: $setParent ? $comment->parent_id : null,
            published_at: $comment->created_at,
            user: $userService->getData($comment->user_id),
            replies: $setReplies ? $comment->repliesCount() : 0,
            likes: $setLikes ? $comment->likesCount() : 0,
            isLiked: $setLiked ? $comment->isLikedBy(Auth::id()) : false,
            content_type: $setContent ? strtolower(class_basename($comment->commentable_type)) : null,
            content_title: $setContent ? $comment->commentable?->title : null, /* @phpstan-ignore-line */
            content_slug: $setContent ? $comment->commentable?->slug : null, /* @phpstan-ignore-line */
        );
    }

    /**
     * @param iterable<Comment> $comments
     * @param bool $setId
     *
     * @return array<CommentData>
     */
    public static function collection(
        iterable $comments,
        bool $setId = false,
        bool $setParent = true,
        bool $setReplies = true,
        bool $setLikes = true,
        bool $setLiked = true,
        bool $setContent = false
    ): array {
        $data = [];
        foreach ($comments as $comment) {
            $data[] = self::toData($comment, $setId, $setParent, $setReplies, $setLikes, $setLiked, $setContent);
        }

        return $data;
    }
}
