<?php

namespace Modules\News\Mappers;

use Illuminate\Support\Facades\Gate;
use Modules\Category\Mappers\CategoryMapper;
use Modules\News\Data\NewsData;
use Modules\News\Models\News;
use Modules\Tag\Mappers\TagMapper;
use Modules\User\Data\UserData;

class NewsMapper
{
    /**
     * Create a NewsData instance from a News model.
     */
    public static function toData(News $news, ?UserData $user = null, bool $setId = false): NewsData
    {
        return new NewsData(
            id: $setId ? $news->id : 0,
            title: $news->title,
            slug: $news->slug,
            content: $news->content,
            excerpt: $news->excerpt,
            image: $news->getCoverImage(),
            published_at: $news->published_at,
            user: $user,
            categories: CategoryMapper::collection($news->categories),
            tags: TagMapper::collection($news->tags),
            canUpdate: Gate::allows('update', $news),
            canDelete: Gate::allows('delete', $news),
        );
    }

    /**
     * @param iterable<News> $newsCollection
     * @param ?UserData $user
     * @param bool $setId
     *
     * @return array<NewsData>
     */
    public static function collection(iterable $newsCollection, ?UserData $user = null, bool $setId = false): array
    {
        $data = [];
        foreach ($newsCollection as $news) {
            $data[] = self::toData($news, $user, $setId);
        }

        return $data;
    }
}
