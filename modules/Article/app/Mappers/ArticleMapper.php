<?php

namespace Modules\Article\Mappers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Article\Data\ArticleData;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;
use Modules\Category\Mappers\CategoryMapper;
use Modules\Tag\Data\TagData;
use Modules\User\Data\UserData;

class ArticleMapper
{
    /**
     * Create a ArticleData instance from a Article model.
     */
    public static function toData(Article $article, UserData $user, bool $setId = false): ArticleData
    {
        $userId = Auth::id();

        return new ArticleData(
            id: $setId ? $article->id : 0,
            title: $article->title,
            slug: $article->slug,
            excerpt: $article->excerpt,
            content: $article->content,
            image: $article->getCoverImage(),
            published_at: $article->published_at,
            user: $user,
            categories: CategoryMapper::collection($article->categories),
            tags: TagData::collect($article->tags)->all(),
            sources: $article->sources,
            likes: $article->likesCount(),
            dislikes: $article->dislikesCount(),
            comments: $article->commentsCount(),
            isLiked: $article->isLikedBy($userId),
            isDisliked: $article->isDislikedBy($userId),
            isSaved: $article->isSavedBy($userId),
            canUpdate: Gate::allows('update', $article),
            canDelete: Gate::allows('delete', $article),
            type: $article->status === ArticleStatus::PUBLISHED ? 'article' : 'draft'
        );
    }
}
