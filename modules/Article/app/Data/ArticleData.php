<?php

namespace Modules\Article\Data;

use DateTime;
use Modules\Category\Data\CategoryData;
use Modules\Tag\Data\TagData;
use Modules\User\Data\UserData;
use Spatie\LaravelData\Data;

class ArticleData extends Data
{
    /**
     * @param array{image: string, responsive: string|array<int, string>, srcset: string, thumb: string|null} $image
     * @param array<CategoryData> $categories
     * @param array<TagData> $tags
     * @param array<int, array{url: string, date: string}> $sources
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly ?string $excerpt,
        public readonly ?string $content,
        public readonly array $image,
        public readonly ?DateTime $published_at,
        public readonly UserData $user,
        public readonly array $categories,
        public readonly array $tags,
        public readonly ?array $sources,
        public readonly int $likes,
        public readonly int $dislikes,
        public readonly int $comments,
        public readonly bool $isLiked,
        public readonly bool $isDisliked,
        public readonly bool $isSaved,
        public readonly bool $canUpdate,
        public readonly bool $canDelete,
        public readonly string $type = 'article'
    ) {
    }
}
